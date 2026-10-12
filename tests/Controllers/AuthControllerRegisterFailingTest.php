<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

/**
 * Unit-тесты для AuthController (ВЕРСИЯ С ЗАВЕДОМО ПРОВАЛЬНЫМИ ТЕСТАМИ)
 */
class AuthControllerRegisterFailingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        parent::tearDown();
    }

    /**
     * FAIL: Ожидаем неправильный текст ошибки
     */
    public function testRegisterWithEmptyEmail(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => '', 'password' => 'password123', 'password_confirm' => 'password123', 'name' => 'John Doe'];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        // Ожидаем несуществующую ошибку, тест упадет
        $this->assertStringContainsString('Email обязателен для заполнения', $output); 
    }

    /**
     * FAIL: Ожидаем неправильный текст ошибки
     */
    public function testRegisterWithInvalidEmail(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => 'not-an-email', 'password' => 'password123', 'password_confirm' => 'password123', 'name' => 'John Doe'];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        // Ожидаем несуществующую ошибку, тест упадет
        $this->assertStringContainsString('Неверный формат почты', $output); 
    }

    /**
     * FAIL: Инвертируем логику (ожидаем, что ошибки НЕ будет, хотя она должна быть)
     */
    public function testRegisterWithShortPassword(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => 'test@example.com', 'password' => '12345', 'password_confirm' => '12345', 'name' => 'John Doe'];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        // Тест упадет, так как контроллер вернет ошибку, а мы утверждаем, что её нет
        $this->assertStringNotContainsString('Пароль должен быть не менее 6 символов', $output); 
    }

    /**
     * FAIL: Ожидаем неправильный текст ошибки
     */
    public function testRegisterWithMismatchedPasswords(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => 'test@example.com', 'password' => 'password123', 'password_confirm' => 'password456', 'name' => 'John Doe'];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Пароли абсолютно разные', $output);
    }

    /**
     * FAIL: Ожидаем неправильный текст ошибки
     */
    public function testRegisterWithEmptyName(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => 'test@example.com', 'password' => 'password123', 'password_confirm' => 'password123', 'name' => ''];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Имя не может быть пустым', $output);
    }

    /**
     * FAIL: Инвертируем логику (ожидаем наличие ошибок на GET запросе)
     */
    public function testRegisterGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        // Тест упадет, так как на GET запросе ошибок нет, а мы ищем их
        $this->assertStringContainsString('Заполните все поля', $output);
    }

    /**
     * FAIL: Ожидаем неправильный текст ошибки
     */
    public function testRegisterWithEmailOnly(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email' => 'test@example.com', 'password' => '', 'password_confirm' => '', 'name' => ''];
        
        $controller = new AuthController();
        ob_start();
        $controller->register();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Не хватает данных', $output);
    }

    /**
     * FAIL: Ожидаем неправильный JSON ответ
     */
    public function testApiRegisterWithEmptyFields(): void
    {
        $controller = new AuthController();
        ob_start();
        $result = $controller->apiRegister();
        $output = ob_get_clean();
        
        $response = json_decode($result, true);
        $this->assertNotNull($response);
        $this->assertArrayHasKey('error', $response);
        // Ожидаем неправильное сообщение
        $this->assertEquals('Ошибка ввода данных', $response['error']); 
    }

    /**
     * FAIL: Инвертируем assertTrue/assertFalse для filter_var
     */
    public function testApiRegisterWithInvalidEmail(): void
    {
        // Тест упадет, так как 'not-an-email' НЕ является валидным, а мы утверждаем обратное
        $this->assertTrue(filter_var('not-an-email', FILTER_VALIDATE_EMAIL) !== false); 
        $this->assertFalse(filter_var('test@example.com', FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * FAIL: Инвертируем проверку длины строки
     */
    public function testApiRegisterWithShortPassword(): void
    {
        // Тест упадет, так как длина '12345' равна 5, а мы утверждаем, что она >= 6
        $this->assertTrue(strlen('12345') >= 6); 
        $this->assertTrue(strlen('123456') < 6);
    }
}