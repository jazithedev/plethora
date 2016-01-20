<?php

/*
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
*/

return [
    ['10 * * * * *', 'route', 'cron_clear_temp'],
    ['*/2 * * * * *', 'function', '\CronJobs\Base::sendQueuedEmails'],
    ['* 1 * * * *', 'function', '\Plethora\Session::sessionCleaner'],
];