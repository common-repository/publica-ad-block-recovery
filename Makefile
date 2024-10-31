PHP_CODESNIFFER=$(GOPATH)/src/github.com/squizlabs/PHP_CodeSniffer

lint:
	php $(PHP_CODESNIFFER)/scripts/phpcs --standard=WordPress-VIP wordpress-publica.php views/settings.php

install:
	if [[ !(-d "$(GOPATH)/src/github.com/squizlabs/PHP_CodeSniffer") ]]; then \
		mkdir -p $(GOPATH)/src/github.com/squizlabs/PHP_CodeSniffer; \
		git clone https://github.com/squizlabs/PHP_CodeSniffer.git $(GOPATH)/src/github.com/squizlabs/PHP_CodeSniffer; \
	fi
	if [[ !(-d "$(GOPATH)/src/github.com/WordPress-Coding-Standards/WordPress-Coding-Standards") ]]; then \
		mkdir -p $(GOPATH)/src/github.com/WordPress-Coding-Standards/WordPress-Coding-Standards; \
		git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git $(GOPATH)/src/github.com/WordPress-Coding-Standards/WordPress-Coding-Standards; \
	fi
	php $(PHP_CODESNIFFER)/scripts/phpcs --config-set installed_paths $(GOPATH)/src/github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
