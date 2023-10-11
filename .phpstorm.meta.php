<?php
namespace PHPSTORM_META {
    
    use BusyPHP\facade\VerifyCode;
    
    registerArgumentsSet('plugin_verify_code_style',
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_DEFAULT |
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_NUMBER |
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_CAPITALIZE |
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_LOWERCASE |
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_ZH |
        \BusyPHP\verifycode\model\PluginVerifyCode::STYLE_LETTER_NUMBER
    );
    expectedArguments(\BusyPHP\verifycode\model\PluginVerifyCode::getStyleMap(), 0, argumentsSet('plugin_verify_code_style'));
    expectedArguments(\BusyPHP\verifycode\model\PluginVerifyCode::create(), 3, argumentsSet('plugin_verify_code_style'));
    
    registerArgumentsSet('verify_code_style',
        \BusyPHP\facade\VerifyCode::STYLE_DEFAULT |
        \BusyPHP\facade\VerifyCode::STYLE_NUMBER |
        \BusyPHP\facade\VerifyCode::STYLE_CAPITALIZE |
        \BusyPHP\facade\VerifyCode::STYLE_LOWERCASE |
        \BusyPHP\facade\VerifyCode::STYLE_ZH |
        \BusyPHP\facade\VerifyCode::STYLE_LETTER_NUMBER
    );
    
    expectedArguments(\BusyPHP\facade\VerifyCode::create(), 3, argumentsSet('verify_code_style'));
}