<?php

//регистрируем рендер
\HTML_QuickForm2_Renderer::register(
    'larakit_form',
    Larakit\QuickForm\LaraFormRenderer::class,
    realpath(__DIR__ . '/QuickForm/LaraFormRenderer.php')
);

//регистрируем команду
Larakit\Boot::register_command(\Larakit\QuickForm\CommandQuickformIde::class);
Larakit\Boot::register_command(\Larakit\QuickForm\CommandQuickformMakeForm::class);

Larakit\Boot::register_provider(\Larakit\QuickForm\LarakitServiceProvider::class);

\Larakit\QuickForm\Register::container(Larakit\QuickForm\LaraForm::class);

\Larakit\StaticFiles\Manager::package('pear/html_quickform2')
    ->cssPackage('quickform.css')
    ->jsPackage('js/quickform.js')
    ->scopeInit('hierselect', ['/packages/pear/html_quickform2/js/quickform-hierselect.js',])
    ->scopeInit('repeat', ['/packages/pear/html_quickform2/js/quickform-repeat.js',])
    ->setSourceDir('data');

\Larakit\QuickForm\Register::register('button_link_twbs', 'qf_button_link_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('button_twbs', 'qf_button_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('checkbox_twbs', 'qf_checkbox_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('group_checkbox_twbs', 'qf_checkbox_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('group_checkbox_button_twbs', 'qf_checkbox_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('daterangepicker_twbs', 'qf_daterangepicker_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('datetime_twbs', 'qf_datetime_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('date_twbs', 'qf_date_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('time_twbs', 'qf_time_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('email_twbs', 'qf_email_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('group_twbs', 'qf_group_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('number_twbs', 'qf_number_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('password_twbs', 'qf_password_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('radio_twbs', 'qf_radio_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('group_radio_twbs', 'qf_group_radio_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('group_radio_button_twbs', 'qf_group_radio_button_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('select2_twbs', 'qf_select2_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('select_twbs', 'qf_select_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('static_twbs', 'qf_static_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('submit_twbs', 'qf_submit_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('switch_twbs', 'qf_switch_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('text_twbs', 'qf_text_twbs', __DIR__ . '/views');
\Larakit\QuickForm\Register::register('textarea_twbs', 'qf_textarea_twbs', __DIR__ . '/views');
