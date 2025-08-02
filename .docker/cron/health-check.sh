#!/bin/sh
if ! pgrep -x crond >/dev/null
then
    echo "crond not running"
    exit
fi