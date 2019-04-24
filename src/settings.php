<?php
return [
    'settings' => [

    	/*
    	 * Root URL of the bridge (the URL which points to public/index.php)
    	 * Secure `https` URL must be used.
    	 * Example: 'https://ssl.example.com/path-to/telegram-web-bridge/'
    	 */
    	'appUrl' => 'https://ssl.example.com/path-to/telegram-web-bridge/',

		/*
		 * Path to SSL certificate file (.crt) used to secure appUrl
		 * You can use Self-Signed certificate too. This certificate
		 * file will be used for setting bridge's webhooks.
		 * Example: '/home/theuser/ssl/certs/ssl_example.com_secret.crt'
		 */
    	'crtPath' => '/home/theuser/ssl/certs/ssl_example.com_secret.crt',

		/*
		 * Send incoming webhook requests from Telegram to your given URL
		 * These will override `setWebhook` request's `url` parameter. However
		 * you still need to send the SSL certification of _this_ bridge
		 * in `certificate` parameter. SSL/HTTPS is not explicitly needed for
		 * the real webhook handler (you apps) however the URL *MUST* still be secret.
		 * Example: 'your-bot-token' => 'http://example.com/secret-path-to-real-webhook-handler',
		 */
		'webhookEndpoints' => [
			'123456789:aBcdEfghIjklm_nOpqRsTuvWXyZaBcDEFjhi' => 'https://example.com/secret-path-to-real-webhook-handler',
		],

		/*
		 * Should API calls (following flow) be logged?
		 * [your_application] ==> [bridge] ==> [telegram]
		 * Set this to true in order to debug bridge
		 */
    	'logApiCalls' => false,

		/*
		 * Should webhook calls (following flow) be logged?
		 * [telegram] ==> [bridge] ==> [your_application]
		 * Set this to true in order to debug bridge
		 */
    	'logWebhookCalls' => false,

		/*
		 * Should errors details be shown in this bridge to end-user?
		 * Set this to true in order to debug bridge
		 * ❌ IT MUST BE FALSE ON PRODUCTION ❌
		 */
        'displayErrorDetails' => false,

        /*
		 * Main logger config
		 */
        'logger' => [
            'name' => 'telegram-web-bridge',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        /*
         * Prevent setting content-length header.
         * It overrides Slim configuration; let it be false
         */
        'addContentLengthHeader' => false,
    ],
];
