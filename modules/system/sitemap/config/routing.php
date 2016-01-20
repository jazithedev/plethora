<?php

# particular page
\Plethora\Router::addRoute('sitemap', '/sitemap')
	->setController('Frontend\Sitemap')
	->setAction('default');