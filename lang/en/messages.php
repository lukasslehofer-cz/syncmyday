<?php

return [
    // Authentication
    'registration_success' => 'Registration successful! Welcome to SyncMyDay.',
    'login_failed' => 'Invalid credentials. Please try again.',
    
    // OAuth
    'oauth_state_mismatch' => 'Security validation failed. Please try again.',
    'oauth_failed' => 'Failed to connect calendar. Please try again.',
    'oauth_cancelled' => 'Calendar connection was cancelled. No changes were made.',
    'calendar_connected' => 'Calendar successfully connected!',
    
    // Connections
    'connection_deleted' => 'Calendar connection removed.',
    'connection_refreshed' => 'Calendar connection refreshed.',
    'connection_refresh_failed' => 'Failed to refresh connection.',
    'need_two_calendars' => 'You need at least 2 connected calendars to create sync rules.',
    
    // Email Calendars
    'email_calendar_created' => 'Email calendar created successfully! Forward calendar invitations to your unique email address.',
    'email_calendar_creation_failed' => 'Failed to create email calendar. Please try again.',
    'email_calendar_deleted' => 'Email calendar connection removed.',
    'connection_deleted_failed' => 'Failed to delete connection.',
    'email_processed_successfully' => 'Email processed successfully! %d event(s) synced.',
    'email_processing_failed' => 'Failed to process email.',
    
    // Sync Rules
    'sync_rule_created' => 'Sync rule created successfully!',
    'sync_rule_updated' => 'Sync rule updated.',
    'sync_rule_deleted' => 'Sync rule deleted.',
    'sync_rule_limit_reached' => 'You\'ve reached the limit for your subscription tier. Upgrade to Pro for unlimited rules.',
    'sync_rule_creation_failed' => 'Failed to create sync rule. Please try again.',
    
    // Billing
    'subscription_required' => 'This feature requires a Pro subscription.',
    'subscription_activated' => 'Pro subscription activated! Enjoy unlimited sync rules.',
    'billing_error' => 'An error occurred. Please try again.',
    'payment_verification_failed' => 'Payment verification failed. Please contact support.',
    'no_subscription' => 'No active subscription found.',
    
    // Onboarding
    'onboarding_complete' => 'Setup complete! Your calendars are now syncing.',
    
    // General
    'success' => 'Success!',
    'error' => 'Error',
    'warning' => 'Warning',
    
    // Calendar Connection Errors (User-Friendly)
    'calendar_connection_unauthorized' => 'Unable to connect :provider calendar. Your account may not have the necessary permissions or services enabled. Please check your :provider account settings and try again.',
    'calendar_unauthorized_hint' => 'Make sure your account has calendar access enabled and all required services are active.',
    'calendar_connection_forbidden' => 'Access to :provider calendar was denied. :hint',
    'calendar_forbidden_hint' => 'Please verify your account has calendar permissions and try reconnecting.',
    'calendar_connection_not_found' => 'The :provider calendar service is temporarily unavailable. Please try again in a few moments.',
    'calendar_connection_rate_limit' => 'Too many connection attempts. Please wait a minute and try again.',
    'calendar_connection_server_error' => ':provider is experiencing technical difficulties. Please try again later.',
    'calendar_invalid_credentials' => 'The connection to :provider could not be established. Please check your account and try again.',
    'calendar_invalid_grant' => 'The authorization has expired or been revoked. Please try connecting again.',
    'calendar_access_denied' => 'You did not grant the necessary permissions. Please try again and allow access to your calendar.',
    'calendar_connection_network_error' => 'Unable to connect to :provider. Please check your internet connection and try again.',
    'calendar_connection_generic_error' => 'Unable to connect :provider calendar. Please try again. If the problem persists, contact support.',
    
    // Sync Rules
    'create_sync_rule' => 'Create Sync Rule',
    'create_sync_rule_description' => 'Set up automatic calendar synchronization',
    'source_calendar' => 'Source Calendar',
    'source_calendar_description' => 'Events from this calendar will create blockers in target calendars',
    'api_calendars' => 'API Calendars (Google/Microsoft)',
    'email_calendars' => 'Email Calendars',
    'select_calendar' => 'Select a calendar',
    'first_select_connection' => 'First select a connection',
    'target_calendars' => 'Target Calendar(s)',
    'target_calendars_description' => 'Blockers will be created in these calendars',
    'add_target' => 'Add another target',
    'blocker_title' => 'Blocker Title',
    'blocker_title_description' => 'This will be the title of blocked events (no details from source)',
    'sync_direction' => 'Sync Direction',
    'one_way' => 'One-way (source → targets only)',
    'two_way' => 'Two-way (bidirectional sync)',
    'filters' => 'Filters',
    'only_busy_events' => 'Only sync "Busy" events (skip Free/Tentative)',
    'ignore_all_day' => 'Ignore all-day events',
    'cancel' => 'Cancel',
    'create_rule' => 'Create Rule',
    'target_email_address' => 'Target Email Address',
    'remove' => 'Remove',
];

