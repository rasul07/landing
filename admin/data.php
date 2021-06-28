<table>
	<tr>
		<td>Общее количество заявок</td>
		<td>Число обработанных заявок</td>
		<td>Число оплаченных заявок</td>
		<td>Число аннулированных заявок</td>
		<td>Общая сумма заявок</td>
		<td>Общая сумма обработанных заявок</td>
		<td>Общая сумма оплаченных заявок</td>
		<td>Общая сумма аннулированных заявок</td>
	</tr>
	<tr>
		<td><?=getCountOrders($data_st)?></td>
		<td><?=getCountConfirmOrders($data_st)?></td>
		<td><?=getCountPayOrders($data_st)?></td>
		<td><?=getCountCancelOrders($data_st)?></td>
		<td><?=getSummaOrders($data_st)?> рублей</td>
		<td><?=getSummaConfirmOrders($data_st)?> рублей</td>
		<td><?=getSummaPayOrders($data_st)?> рублей</td>
		<td><?=getSummaCancelOrders($data_st)?> рублей</td>
	</tr>
</table>