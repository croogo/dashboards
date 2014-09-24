<?php
$this->viewVars['title_for_layout'] = __d('dashboards', 'Dashboards');
$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('dashboards', 'Dashboards'), array('action' => 'index'));

$this->set('showActions', false);

$this->append('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('column'),
		$this->Paginator->sort('collapsed'),
		$this->Paginator->sort('status'),
		$this->Paginator->sort('updated'),
		$this->Paginator->sort('created'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
foreach ($dashboards as $dashboard):
?>
	<tr>
		<td><?php echo h($dashboard['DashboardsDashboard']['id']); ?>&nbsp;</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['alias']); ?>&nbsp;</td>
		<td><?php echo $this->Dashboards->columnName($dashboard['DashboardsDashboard']['column']); ?>&nbsp;</td>
		<td><?php echo $this->Layout->status($dashboard['DashboardsDashboard']['collapsed']); ?></td>
		<td><?php echo $this->Layout->status($dashboard['DashboardsDashboard']['status']); ?></td>
		<td><?php echo h($dashboard['DashboardsDashboard']['updated']); ?>&nbsp;</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['created']); ?>&nbsp;</td>
		<td class="item-actions">
			<?php echo $this->Croogo->adminRowActions($dashboard['DashboardsDashboard']['id']); ?>
			<?php echo $this->Croogo->adminRowAction('', array('action' => 'delete', $dashboard['DashboardsDashboard']['id']), array('icon' => 'trash', 'escape' => true), __d('croogo', 'Are you sure you want to delete # %s?', $dashboard['DashboardsDashboard']['id'])); ?>
		</td>
	</tr>
<?php
endforeach;
$this->end();
