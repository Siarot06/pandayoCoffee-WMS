<div class="row">
    <div class="col-md-8">
        <div class="card card-outline rounded-5 card-dark">
            <div class="card-header">
                <h3 class="card-title">Request Leave History</h3>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <table id="leave-table" class="table table-hover table-striped table-bordered text-center">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $i = 1;
                                if(isset($_POST['id'])){
                                    $id = $_POST['id'];
                                    $query = "DELETE FROM wh_leave_request WHERE id = $id";
                                    $result = pg_query($conn, $query);
                                }
                                $qry = pg_query($conn, "SELECT * from wh_leave_request WHERE employeeid = '".$_settings->userdata('id')."' ORDER by id DESC");

                                while($row = pg_fetch_assoc($qry)):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?= $row['from_date'] . ' - ' . $row['to_date'] ?></td>
                                <td><?= $row['reason'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td align="center">
                                    <button class="btn btn-flat btn-danger btn-xs bg-gradient-danger" onclick="deleteRow(<?= $row['id'] ?>)"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>

                        <script>
                            function deleteRow(id) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "You won't be able to revert this!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: '',
                                            type: 'POST',
                                            data: { id: id },
                                            success: function(response) {
                                                location.reload();
                                            },
                                            error: function(jqXHR, textStatus, errorThrown) {
                                                console.log(textStatus, errorThrown);
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card card-outline rounded-5 card-dark">
            <div class="card-header">
                <h3 class="card-title">File a Leave</h3>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <?php
                        if (isset($_POST['approve'])) {
                            $employee_id = pg_escape_string($conn, $_POST['employee_id']);
                            $department = pg_escape_string($conn, $_POST['department']);
                            $employee_name = pg_escape_string($conn, $_POST['employee_name']);
                            $email = pg_escape_string($conn, $_POST['email']);
                            $contact_number = pg_escape_string($conn, $_POST['contact_number']);
                            $reason = pg_escape_string($conn, $_POST['reason']);
                            $from = pg_escape_string($conn, $_POST['from']);
                            $to = pg_escape_string($conn, $_POST['to']);

                            $result = pg_query_params($conn, "INSERT INTO wh_leave_request (employeeid, name, email, contact, reason, from_date, to_date, status, date_requested) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)", array($employee_id, $employee_name, $email, $contact_number, $reason, $from, $to, 'Pending', date('Y-m-d')));
                        }
                    ?>

                    <form class="mt-5 mt-md-0" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee-id">Employee ID</label>
                                    <input type="text" class="form-control form-control-sm" id="employee-id" name="employee_id" value="<?php echo ucwords($_settings->userdata('id')) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" class="form-control form-control-sm" id="department" name="department" value="<?php echo ucwords($_settings->userdata('department')) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="employee-name">Employee Name</label>
                            <input type="text" class="form-control form-control-sm" id="employee-name" name="employee_name" value="<?php echo ucwords($_settings->userdata('fullname')) ?>" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo ucwords($_settings->userdata('email')) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="number" class="form-control form-control-sm" id="contact_number" name="contact_number" value="<?php echo ucwords($_settings->userdata('contact')) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea rows="4" class="form-control form-control-sm" id="reason" name="reason" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from">From</label>
                                    <input type="date" class="form-control form-control-sm" id="from" name="from" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to">To</label>
                                    <input type="date" class="form-control form-control-sm" id="to" name="to" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                        <button class="btn btn-dark bg-gradient-success border" name="approve"> Send Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function(){
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [3] }
			],
			order:[0,'asc']
		});
	})
</script>