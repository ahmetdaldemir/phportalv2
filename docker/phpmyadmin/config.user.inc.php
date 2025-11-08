<?php
/**
 * PHPMyAdmin Configuration
 */

// Basic settings
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['MaxSizeForInputField'] = 200 * 1024 * 1024; // 200MB
$cfg['MemoryLimit'] = '2048M';

// Session settings
$cfg['LoginCookieValidity'] = 1440;
$cfg['LoginCookieStore'] = 0;
$cfg['LoginCookieDeleteAll'] = true;

// Display settings
$cfg['MaxRows'] = 1000;
$cfg['Order'] = 'ASC';

// Export/Import settings
$cfg['Export']['compression'] = 'none';
$cfg['Export']['format'] = 'sql';
$cfg['Export']['charset'] = 'utf-8';
$cfg['Import']['charset'] = 'utf-8';
$cfg['Import']['allow_interrupt'] = true;

// Error reporting
$cfg['Error_Handler']['display'] = true;
$cfg['Error_Handler']['gather'] = true;

// Theme
$cfg['ThemeDefault'] = 'pmahomme';
$cfg['ThemeManager'] = true;

// Server settings (simplified)
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = 'mysql';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['user'] = 'phportal';
$cfg['Servers'][$i]['password'] = 'phportal123';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
