#!/bin/bash
if [[ ! -d /var/log/muonium ]]; then
  mkdir -p /var/log/muonium
fi
case $1 in
  "inactive_users")
    echo "$(date) :: inactive users deleted. [OK]" >> /var/log/muonium/days.log
  ;;
  "reset_counter")
    echo "$(date) :: reset pp counter. [OK]" >> /var/log/muonium/ppCounter.log
  ;;
  *)
    echo "$(date) :: executed [Error]"
  ;;
esac
