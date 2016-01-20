<?php

# default action
\Plethora\Router::addRoute('filemanager', '/filemanager/{fmaction}.php')
	->addParameterType('fmaction', 'string')
	->setController('Tinymcefilemanager');