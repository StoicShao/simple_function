#!/bin/sh
cd /usr/local/coreseek && bin/searchd -c etc/conf_505.conf --stop
cd /usr/local/coreseek && bin/indexer -c etc/conf_505.conf --all
cd /usr/local/coreseek && bin/searchd -c etc/conf_505.conf

