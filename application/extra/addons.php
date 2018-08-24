<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'sms_send' => 
    array (
      0 => 'clsms',
    ),
    'sms_notice' => 
    array (
      0 => 'clsms',
    ),
    'sms_check' => 
    array (
      0 => 'clsms',
    ),
    'leesignhook' => 
    array (
      0 => 'leesign',
    ),
    'admin_login_init' => 
    array (
      0 => 'loginbg',
    ),
  ),
  'route' => 
  array (
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
    '/leesign$' => 'leesign/index/index',
    '/third$' => 'third/index/index',
    '/third/connect/[:platform]' => 'third/index/connect',
    '/third/callback/[:platform]' => 'third/index/callback',
  ),
);