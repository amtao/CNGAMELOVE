#!/bin/sh
PREFIX=/data/www/lyjxtime/s1_lyjxtime/queue
INTERVAL=3
R_CONFIG="QUEUE=* COUNT=1 INTERVAL=3 REDIS_BACKEND=10.253.19.118:6382"
COM_PHP=/usr/local/services/php/bin/php
LOG_NAME=log/lyjxtime_resque.log
PID_NAME=pid/lyjxtime_resque.pid
QUEUE=* COUNT=1 INTERVAL=3 REDIS_BACKEND=10.253.19.118:6382 nohup ${COM_PHP} ${PREFIX}/resque/resque_lyjxtime.php > ${PREFIX}/${LOG_NAME} 2>&1 & echo $! > ${PREFIX}/${PID_NAME}
while [ 1 ]; do
    if [ ! -d /proc/`cat ${PREFIX}/${PID_NAME}` ]; then
        QUEUE=* COUNT=1 INTERVAL=3 REDIS_BACKEND=10.253.19.118:6382 nohup ${COM_PHP} ${PREFIX}/resque/resque_lyjxtime.php > ${PREFIX}/${LOG_NAME} 2>&1 & echo $! > ${PREFIX}/${PID_NAME}
        echo 'NEW_PID:'`cat ${PREFIX}/${PID_NAME} && date '+%Y-%m-%d %H:%M:%S'`
    fi
    sleep ${INTERVAL}
done