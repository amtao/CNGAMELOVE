#!/bin/bash
#��̨���к���exit�˳������˳��󻹿��Լ�����̨����
QUEUE=* COUNT=1 INTERVAL=3 VVERBOSE=1 REDIS_BACKEND=10.253.19.118:6382 /usr/local/services/php/bin/php /data/www/lyjxtime/s1_lyjxtime/queue/Resque.php 2>&1 &