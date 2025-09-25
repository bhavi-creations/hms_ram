<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
   <div class="card-header">
       <h3 class="card-title">New Lab Order</h3>
   </div>
   <div class="card-body">
       <form action="<?= base_url('laboratory/orders/save') ?>" method="post">
           <div class="form-group">
               <label for="patient_id">Select Patient</label>
               <select name="patient_id" id="patient_id" class="form-control select2" required>
                   <option value="">Select Patient</option>
               </select>
           </div>

           <div class="form-group">
               <label>Phone Number</label>
               <input type="text" id="patient_phone" class="form-control" readonly />
           </div>
           <div class="form-group">
               <label>Doctor Name</label>
               <input type="text" id="patient_doctor" class="form-control" readonly />
           </div>

           <div class="form-group">
               <label for="test_ids">Select Tests</label>
               <select name="test_ids[]" id="test_ids" class="form-control select2" multiple required>
                   <?php foreach ($labTests as $test): ?>
                       <option value="<?= esc($test['id']) ?>"><?= esc($test['name']) ?> - <?= esc($test['price']) ?></option>
                   <?php endforeach; ?>
               </select>
           </div>

           <div class="form-group">
               <label for="remarks">Remarks</label>
               <textarea name="remarks" id="remarks" class="form-control"></textarea>
           </div>

           <button type="submit" class="btn btn-success">Place Order</button>
           <a href="<?= base_url('laboratory/orders') ?>" class="btn btn-secondary">Cancel</a>
       </form>
   </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#patient_id').select2({
        placeholder: 'Search by Patient ID Code',
        ajax: {
            url: '<?= base_url('patients/search') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            phone: item.phone,
                            doctor: item.doctor
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#patient_id').on('select2:select', function(e) {
        var data = e.params.data;
        $('#patient_phone').val(data.phone);
        $('#patient_doctor').val(data.doctor);
    });

    $('#test_ids').select2();
});
</script>
<?= $this->endSection() ?>
