php-pear:
  pkg.installed:
    - require:
      - pkg: php

# Install memcached extension
libmemcached-dev:
  pkg.installed

memcached:
  pecl.installed:
    - require:
      - pkg: php-pear
      - pkg: libmemcached-dev

/etc/php5/conf.d/memcached.ini:
  file.managed:
    - source: salt://php/files/memcached.ini
    - user: root
    - group: root
    - mode: 644

# Install Zend OpCache extension
ZendOpcache:
  pecl.installed:
    - require: 
      - pkg: php-pear

/etc/php5/conf.d/opcache.ini:
  file.managed:
    - source: salt://php/files/opcache.ini
    - user: root
    - group: root
    - mode: 644

# Install couchbase extension
include:
  - couchbase.libs

couchbase:
  pecl.installed:
    - require:
      - pkg: php-pear
      - pkg: libcouchbase-dev
      - pkg: libcouchbase2-libevent

