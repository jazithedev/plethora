<?php

# particular page
\Plethora\Router::addRoute('page', '/page/{rewrite}')
	->addParameterType('rewrite', 'string')
	->setController('Frontend\Pages')
	->setAction('page');