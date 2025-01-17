<?php if($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
	</script>
<?php endif;?>


<style>
	.enroll-logo{
		width:3em;
		height:3em;
		object-fit:cover;
		object-position:center center;
	}
</style>


<div class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<h3 class="card-title">Stock Adjustment</h3>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered text-center" id="list">
				<colgroup>
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Item</th>
						<th>Unit</th>
						<th>Current Stock</th>
						<th>Last Updated</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT i.*, c.name AS `category`, 
											(COALESCE((SELECT SUM(quantity) FROM `stockin_list` WHERE item_id = i.id), 0) 
											- COALESCE((SELECT SUM(quantity) FROM `stockout_list` WHERE item_id = i.id), 0)
											+ COALESCE((SELECT SUM(quantity) FROM `stockin_list_deleted` WHERE item_id = i.id), 0)
											- COALESCE((SELECT SUM(quantity) FROM `waste_list` WHERE item_id = i.id), 0)) AS `available`, 
											COALESCE((SELECT date_updated FROM `stockin_list` WHERE item_id = i.id ORDER BY date_updated DESC LIMIT 1), 
													(SELECT date_updated FROM `stockin_list_deleted` WHERE item_id = i.id ORDER BY date_updated DESC LIMIT 1)) AS `last_updated`
										FROM `item_list` i 
										INNER JOIN category_list c ON i.category_id = c.id 
										WHERE i.delete_flag = 0 
										ORDER BY i.date_updated DESC");
						
						while($row = $qry->fetch_assoc()):
							$name = $row['name'];
							$available_quantity = (int)$row['available'];
                            
					?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td class="">
								<div style="line-height:1em">
									<div><?= $row['name'] ?></div>
									<div class="small"><i><?= $row['category'] ?></i></div>
								</div>
							</td>
							<td><?= $row['unit'] ?></td>
							<td><?= (int)$row['available'] ?></td>
							<td><?php echo !empty($row['last_updated']) ? date("Y-m-d H:i",strtotime($row['last_updated'])) : ''; ?></td>

							<td>
								<a class="btn btn-flat btn-sm btn-light bg-gradient-light border" href="./?page=stocks/view_stock&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> Adjust</a>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this enroll permanently?","delete_enroll",[$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [5] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	function delete_enroll($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_enroll",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>