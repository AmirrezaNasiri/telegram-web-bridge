<?php

namespace Tests\Functional;

use Slim\Http\UploadedFile;

class ApiCallTest extends BaseTestCase
{
    /**
     * Test `getMe` Bot API method
     */
    public function testGetMe()
    {
		// $this->markTestSkipped('getMe test is working');
		$response = $this->runApiCall('getMe');
        $this->isOk($response);
    }

	/**
	 * Test `sendMessage` Bot API method
	 */
	public function testSendMessage()
	{
		// $this->markTestSkipped('sendMessage test is working');
		$response = $this->runApiCall('sendMessage', [
			'chat_id' => $this->settings['chatId'],
			'text' => 'This is a *test* _message_ sent by `telegram-web-bridge`.',
			'parse_mode' => 'markdown',
		]);
		$this->isOk($response);
	}

	/**
	 * Test `sendPhoto` Bot API method
	 */
	public function testSendPhoto()
	{
		// $this->markTestSkipped('sendPhoto test is working');
		$response = $this->runApiCall('sendPhoto', [
			'chat_id' => $this->settings['chatId'],
			'caption' => 'This is a *test* _image_ with caption sent by `telegram-web-bridge.`',
			'parse_mode' => 'markdown',
		], [
			'photo' => __DIR__ . '/../Assets/photo-1.png'
		]);
		$this->isOk($response);
	}
}