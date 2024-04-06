SHELL = /bin/bash
### https://makefiletutorial.com/


update: stop git-up run
	@echo "Success deploy"

git-up:
	@git pull

run:
	@nohup php -S 127.0.0.1:889 index.php > php.log 2>&1 & echo "$$!" > pid.txt

stop:
	@kill -9 `cat pid.txt`
	@rm pid.txt



run-dev:
	php -S 0.0.0.0:80 index.php