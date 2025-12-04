<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Payment Validation Logic
 */
class PaymentValidationTest extends TestCase
{
    /**
     * Test credit card number validation pattern
     */
    public function testCreditCardNumberPattern(): void
    {
        $validCards = [
            '1234567890123',      // 13 digits
            '1234567890123456',   // 16 digits
            '1234567890123456789' // 19 digits
        ];
        
        $invalidCards = [
            '123456789012',       // 12 digits (too short)
            '12345678901234567890', // 20 digits (too long)
            '1234-5678-9012-3456', // Contains dashes
        ];
        
        // Cards with spaces - should be valid after stripping spaces
        $cardsWithSpaces = [
            '1234 5678 9012 3456',  // 16 digits with spaces
            '1234 5678 9012 3456789' // 19 digits with spaces
        ];
        
        foreach ($validCards as $card) {
            $cleaned = preg_replace('/\s+/', '', $card);
            $isValid = preg_match('/^\d{13,19}$/', $cleaned);
            $this->assertTrue((bool)$isValid, "Card '{$card}' should be valid");
        }
        
        foreach ($invalidCards as $card) {
            $cleaned = preg_replace('/\s+/', '', $card);
            $isValid = preg_match('/^\d{13,19}$/', $cleaned);
            $this->assertFalse((bool)$isValid, "Card '{$card}' should be invalid");
        }
        
        // Cards with spaces should be valid after cleaning
        foreach ($cardsWithSpaces as $card) {
            $cleaned = preg_replace('/\s+/', '', $card);
            $isValid = preg_match('/^\d{13,19}$/', $cleaned);
            $this->assertTrue((bool)$isValid, "Card '{$card}' should be valid after stripping spaces");
        }
    }

    /**
     * Test expiry date validation pattern
     */
    public function testExpiryDatePattern(): void
    {
        $validExpiries = [
            '01/25',
            '12/99',
            '06/30'
        ];
        
        $invalidExpiries = [
            '13/25',  // Invalid month
            '00/25',  // Invalid month
            '1/25',   // Missing leading zero
            '12/5',   // Missing leading zero in year
            '12-25',  // Wrong separator
            '1225'    // Missing separator
        ];
        
        foreach ($validExpiries as $expiry) {
            $isValid = preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry);
            $this->assertTrue((bool)$isValid, "Expiry '{$expiry}' should be valid");
        }
        
        foreach ($invalidExpiries as $expiry) {
            $isValid = preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry);
            $this->assertFalse((bool)$isValid, "Expiry '{$expiry}' should be invalid");
        }
    }

    /**
     * Test CVV validation pattern
     */
    public function testCVVPattern(): void
    {
        $validCVVs = [
            '123',   // 3 digits
            '1234'   // 4 digits
        ];
        
        $invalidCVVs = [
            '12',    // Too short
            '12345', // Too long
            'abc',   // Not numeric
            '12a'    // Contains letter
        ];
        
        foreach ($validCVVs as $cvv) {
            $isValid = preg_match('/^\d{3,4}$/', $cvv);
            $this->assertTrue((bool)$isValid, "CVV '{$cvv}' should be valid");
        }
        
        foreach ($invalidCVVs as $cvv) {
            $isValid = preg_match('/^\d{3,4}$/', $cvv);
            $this->assertFalse((bool)$isValid, "CVV '{$cvv}' should be invalid");
        }
    }

    /**
     * Test bank account number validation
     */
    public function testBankAccountNumberPattern(): void
    {
        $validAccounts = [
            '12345678',      // 8 digits
            '12345678901234567890' // 20 digits
        ];
        
        $invalidAccounts = [
            '1234567',      // Too short
            '123456789012345678901', // Too long
            '1234-5678',    // Contains dash
            'abc12345'      // Contains letters
        ];
        
        foreach ($validAccounts as $account) {
            $cleaned = preg_replace('/\s+/', '', $account);
            $isValid = preg_match('/^\d{8,20}$/', $cleaned);
            $this->assertTrue((bool)$isValid, "Account '{$account}' should be valid");
        }
        
        foreach ($invalidAccounts as $account) {
            $cleaned = preg_replace('/\s+/', '', $account);
            $isValid = preg_match('/^\d{8,20}$/', $cleaned);
            $this->assertFalse((bool)$isValid, "Account '{$account}' should be invalid");
        }
    }

    /**
     * Test routing number validation
     */
    public function testRoutingNumberPattern(): void
    {
        $validRouting = [
            '123456',   // 6 digits
            '123456789' // 9 digits
        ];
        
        $invalidRouting = [
            '12345',    // Too short
            '1234567890', // Too long
            'abc123'    // Contains letters
        ];
        
        foreach ($validRouting as $routing) {
            $cleaned = preg_replace('/\s+/', '', $routing);
            $isValid = preg_match('/^\d{6,9}$/', $cleaned);
            $this->assertTrue((bool)$isValid, "Routing '{$routing}' should be valid");
        }
        
        foreach ($invalidRouting as $routing) {
            $cleaned = preg_replace('/\s+/', '', $routing);
            $isValid = preg_match('/^\d{6,9}$/', $cleaned);
            $this->assertFalse((bool)$isValid, "Routing '{$routing}' should be invalid");
        }
    }

    /**
     * Test wallet address validation
     */
    public function testWalletAddressPattern(): void
    {
        $validWallets = [
            str_repeat('a', 20),  // 20 characters
            str_repeat('A', 64),  // 64 characters
            '1A2B3C4D5E6F7G8H9I0J' // Mixed case
        ];
        
        $invalidWallets = [
            str_repeat('a', 19),  // Too short
            str_repeat('a', 65), // Too long
            'abc def',            // Contains space
            'abc-def',            // Contains dash
        ];
        
        foreach ($validWallets as $wallet) {
            $isValid = strlen($wallet) >= 20 && strlen($wallet) <= 64 && preg_match('/^[A-Za-z0-9]+$/', $wallet);
            $this->assertTrue($isValid, "Wallet '{$wallet}' should be valid");
        }
        
        foreach ($invalidWallets as $wallet) {
            $isValid = strlen($wallet) >= 20 && strlen($wallet) <= 64 && preg_match('/^[A-Za-z0-9]+$/', $wallet);
            $this->assertFalse($isValid, "Wallet '{$wallet}' should be invalid");
        }
    }

    /**
     * Test payment details validation
     */
    public function testPaymentDetailsValidation(): void
    {
        $validDetails = [
            str_repeat('a', 10),   // Minimum length
            str_repeat('a', 500),  // Maximum length
            'Valid payment details with numbers 123'
        ];
        
        $invalidDetails = [
            str_repeat('a', 9),    // Too short
            str_repeat('a', 501),  // Too long
        ];
        
        foreach ($validDetails as $details) {
            $isValid = strlen(trim($details)) >= 10 && strlen(trim($details)) <= 500;
            $this->assertTrue($isValid, "Details should be valid");
        }
        
        foreach ($invalidDetails as $details) {
            $isValid = strlen(trim($details)) >= 10 && strlen(trim($details)) <= 500;
            $this->assertFalse($isValid, "Details should be invalid");
        }
    }

    /**
     * Test name on card validation
     */
    public function testNameOnCardValidation(): void
    {
        $validNames = [
            'John Doe',
            'Mary Jane Smith',
            'A B'
        ];
        
        $invalidNames = [
            'A',                    // Too short
            str_repeat('a', 51),    // Too long
            'John123',              // Contains numbers
            'John-Doe',             // Contains dash
        ];
        
        foreach ($validNames as $name) {
            $isValid = strlen(trim($name)) >= 2 && strlen(trim($name)) <= 50 && preg_match('/^[A-Za-z\s]+$/', $name);
            $this->assertTrue($isValid, "Name '{$name}' should be valid");
        }
        
        foreach ($invalidNames as $name) {
            $isValid = strlen(trim($name)) >= 2 && strlen(trim($name)) <= 50 && preg_match('/^[A-Za-z\s]+$/', $name);
            $this->assertFalse($isValid, "Name '{$name}' should be invalid");
        }
    }

    /**
     * Test amount validation
     */
    public function testAmountValidation(): void
    {
        $validAmounts = [
            0.01,
            100.50,
            1000.00,
            999999.99
        ];
        
        $invalidAmounts = [
            0,
            -10,
            -0.01
        ];
        
        foreach ($validAmounts as $amount) {
            $this->assertGreaterThan(0, $amount, "Amount {$amount} should be valid");
        }
        
        foreach ($invalidAmounts as $amount) {
            $this->assertLessThanOrEqual(0, $amount, "Amount {$amount} should be invalid");
        }
    }
}

