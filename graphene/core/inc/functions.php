<?php

function dd($data): void
{
    getBootstrap();

    echo "<pre style='background: #253139; color: #fff; font-size: 16px; cursor: default; padding: 16px; border-radius: 4px; width: fit-content; min-width: 700px;'>";
    print_r($data);
    echo "</pre>";
    exit();
}

function error($message): void
{
    if (is_array($message)) {
        ob_start();
        echo '<pre>';
        print_r($message);
        echo '</pre>';
        $message = ob_get_contents();
        ob_end_clean();
    }

    getBootstrap();
    exit("<div class='container' style='padding: 20px;'><div class='alert alert-danger'>$message</div></div>");
}

function getBootstrap(): void
{
    if (!isset($GLOBALS['bootstrapIsInclude'])) {
        $version = '5.2.0';
        echo '<link href="/graphene/resources/vendors/bootstrap/' . $version . '/bootstrap.min.css" rel="stylesheet">';
        echo '<script src="/graphene/resources/vendors/bootstrap/' . $version . '/bootstrap.bundle.min.js"></script>';
        $GLOBALS['bootstrapIsInclude'] = true;
    }
}