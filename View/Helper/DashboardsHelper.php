<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Dashboards Helper
 *
 * @category Helper
 * @package  Croogo.Dashboards.View.Helper
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsHelper extends AppHelper {

	public $helpers = array(
		'Croogo.Layout',
		'Html' => array('className' => 'Croogo.CroogoHtml'),
	);

/**
 * Before Render callback
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			Croogo::dispatchEvent('Croogo.setupAdminDashboardData', $this->_View);
		}
	}

	public function dashboards() {
		$dashboards = Configure::read('Dashboards');
		$userId = AuthComponent::user('id');
		if (empty($userId)) {
			return '';
		}

		$columns = array(
			CroogoDashboard::LEFT => array(),
			CroogoDashboard::RIGHT => array(),
			CroogoDashboard::FULL => array(),
		);
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Croogo.Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		$cssSetting = $this->Layout->themeSetting('css');

		if (!empty($this->_View->viewVars['boxes_for_dashboard'])) {
			$boxesForLayout = Hash::combine($this->_View->viewVars['boxes_for_dashboard'], '{n}.DashboardsDashboard.alias', '{n}.DashboardsDashboard');
			foreach ($boxesForLayout as $alias => $userBox) {
				if (isset($dashboards[$alias])) {
					$dashboards[$alias] = array_merge($dashboards[$alias], $userBox);
				}
			}

			$dashboards = Hash::sort($dashboards, '{s}.order', 'ASC');
		} else {
			$dashboards = Hash::sort($dashboards, '{s}.weight', 'ASC');
		}

		foreach ($dashboards as $alias => $dashboard) {
			if ($currentRole != 'admin' && !in_array($currentRole, $dashboard['access'])) {
				continue;
			}

			$opt = array(
				'alias' => $alias,
				'dashboard' => $dashboard,
			);
			Croogo::dispatchEvent('Croogo.beforeRenderDashboard', $this->_View, compact('alias', 'dashboard'));
			$dashboardBox = $this->_View->element('Extensions.admin/dashboard', $opt);
			Croogo::dispatchEvent('Croogo.afterRenderDashboard', $this->_View, compact('alias', 'dashboard', 'dashboardBox'));

			if ($dashboard['column'] === false) {
				$dashboard['column'] = count($columns[0]) <= count($columns[1]) ? CroogoDashboard::LEFT : CroogoDashboard::RIGHT;
			}

			$columns[$dashboard['column']][] = $dashboardBox;
		}

		$columnDivs = array(
			0 => $this->Html->tag('div', implode('', $columns[CroogoDashboard::LEFT]), array('class' => $cssSetting['dashboardLeft'] . ' sortable-column', 'id' => 'column-0')),
			1 => $this->Html->tag('div', implode('', $columns[CroogoDashboard::RIGHT]), array('class' => $cssSetting['dashboardRight'] . ' sortable-column', 'id' => 'column-1')),
		);
		$fullDiv = $this->Html->tag('div', implode('', $columns[CroogoDashboard::FULL]), array('class' => 'span12 sortable-column', 'id' => 'column-2'));

		return $this->Html->tag('div', $fullDiv, array('class' => $cssSetting['row'])) .
				$this->Html->tag('div', implode('', $columnDivs), array('class' => $cssSetting['row']));
	}

}