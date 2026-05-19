<?php

namespace Tests\Unit;

use App\Services\Calendar\MicrosoftCalendarService;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Microsoft\Graph\Exception\GraphException;
use ReflectionMethod;
use Tests\TestCase;

class MicrosoftCalendarServiceRetryTest extends TestCase
{
    private function callExecuteWithRetry(MicrosoftCalendarService $service, callable $fn, string $op = 'test'): mixed
    {
        $method = new ReflectionMethod($service, 'executeWithRetry');
        $method->setAccessible(true);

        return $method->invoke($service, $fn, $op, []);
    }

    private function guzzleServerException(int $status, array $headers = []): ServerException
    {
        $request = new Request('POST', 'https://graph.microsoft.com/v1.0/me/events');
        $response = new Response($status, $headers, '{"error":{"code":"UnknownError"}}');

        return new ServerException("Server error: {$status}", $request, $response);
    }

    public function test_retries_504_then_succeeds(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $result = $this->callExecuteWithRetry($service, function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw $this->guzzleServerException(504);
            }

            return 'ok';
        });

        $this->assertSame('ok', $result);
        $this->assertSame(3, $attempts);
    }

    public function test_does_not_retry_404(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $this->expectException(GraphException::class);

        try {
            $this->callExecuteWithRetry($service, function () use (&$attempts) {
                $attempts++;
                throw new GraphException('Not found', 404);
            });
        } finally {
            $this->assertSame(1, $attempts, '404 must not be retried');
        }
    }

    public function test_does_not_retry_401(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $this->expectException(GraphException::class);

        try {
            $this->callExecuteWithRetry($service, function () use (&$attempts) {
                $attempts++;
                throw new GraphException('Unauthorized', 401);
            });
        } finally {
            $this->assertSame(1, $attempts, '401 must not be retried');
        }
    }

    public function test_retries_on_429_and_respects_retry_after_header(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $start = microtime(true);
        $result = $this->callExecuteWithRetry($service, function () use (&$attempts) {
            $attempts++;
            if ($attempts === 1) {
                throw $this->guzzleServerException(429, ['Retry-After' => '2']);
            }

            return 'ok';
        });
        $elapsed = microtime(true) - $start;

        $this->assertSame('ok', $result);
        $this->assertSame(2, $attempts);
        $this->assertGreaterThanOrEqual(2.0, $elapsed, 'Retry-After: 2 must be honored');
    }

    public function test_retries_connect_exception(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $result = $this->callExecuteWithRetry($service, function () use (&$attempts) {
            $attempts++;
            if ($attempts < 2) {
                throw new ConnectException(
                    'Connection timed out',
                    new Request('GET', 'https://graph.microsoft.com/v1.0/me')
                );
            }

            return 'ok';
        });

        $this->assertSame('ok', $result);
        $this->assertSame(2, $attempts);
    }

    public function test_gives_up_after_max_attempts(): void
    {
        $service = new MicrosoftCalendarService;
        $attempts = 0;

        $this->expectException(ServerException::class);

        try {
            $this->callExecuteWithRetry($service, function () use (&$attempts) {
                $attempts++;
                throw $this->guzzleServerException(504);
            });
        } finally {
            $this->assertSame(4, $attempts, 'Must attempt 4 times total (1 initial + 3 retries)');
        }
    }
}
