<?php
/**
 *
 * Created by thinkcmf5_1.
 * User: wuzz
 * Date: 2019/8/7
 * Time: 10:56 PM
 */

//日志加载
function log4($config = 'DevLog')
{
    require_once('../vendor/log4php/Logger.php');
    Logger::configure('./log4php.ini');
    return Logger::getLogger('DevLog');
}
