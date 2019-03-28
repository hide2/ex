#!/bin/sh

#1 * * * * /root/ex/rotate.sh

NOW=`date +%Y-%m-%d_%H`
cp /root/ex/logs/ex.log /root/ex/logs/ex.log.${NOW}
> /root/ex/logs/ex.log