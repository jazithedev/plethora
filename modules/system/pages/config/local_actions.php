<?php

\Plethora\Router\LocalActions::addLocalAction(__('Edit page'), 'page', 'backend')
	->setParameters(array(
		'controller' => 'pages',
		'action'	 => 'edit',
	))
	->setBuilder(function(\Plethora\Router\LocalActions\Action $oAction) {
		$sPageRewrite	 = (int) \Plethora\Router::getParam('rewrite');
		$aPage			 = \Plethora\DB::query('SELECT p.id FROM \Model\Page p WHERE p.rewrite = :rewrite')->param('rewrite', $sPageRewrite)->single();

		$oAction->setParameter('id', $aPage['id']);
	});

\Plethora\Router\LocalActions::addLocalAction(__('Preview'), 'backend', 'page')
	->setConditions(array(
		'controller' => 'pages',
		'action'	 => 'edit',
	))
	->setBuilder(function(\Plethora\Router\LocalActions\Action $oAction) {
		$iNewsID		 = (int) \Plethora\Router::getParam('id');
		$oPage			 = \Plethora\DB::find('Model\Page', $iNewsID); /* @var $oPage \Model\Page */

		$oAction->setParameter('rewrite', $oPage->getRewrite());
	});
