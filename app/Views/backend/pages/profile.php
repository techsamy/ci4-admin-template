<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Profile</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('admin.home'); ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Profile
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
        <div class="pd-20 card-box height-100-p">
            <div class="profile-photo">
                <a href="javascript:;" onclick="event.preventDefault(); document.getElementById('user_profile_file').click();" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                <input type="file" name="user_profile_file" id="user_profile_file" class="d-none" style="opacity: 0"/>
                <?php 
                    $avatar = getUser()->picture 
                    ? base_url('/images/users/' . getUser()->picture) 
                    : base_url('/images/users/default-avatar.png');
                ?>
                <img src="<?= $avatar ?>" alt="" class="avatar-photo ci-avatar-photo" />
            </div>
            <h5 class="text-center h5 mb-0 ci-user-name"><?= getUser()->name; ?></h5>
            <p class="text-center text-muted font-14 ci-user-email">
                <?= getUser()->email; ?>
            </p>
        </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
        <div class="card-box height-100-p overflow-hidden">
            <div class="profile-tab height-100-p">
                <div class="tab height-100-p">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#personal_info"
                                role="tab">Personal Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#change_password" role="tab">Change Password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Personal Info Tab start -->
                        <div class="tab-pane fade show active" id="personal_info" role="tabpanel">
                            <div class="pd-20">
                                <form action="<?= route_to('update-personal-details'); ?>" method="post" id="update-personal-details">
                                    <?= csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" placeholder="Enter full Name" name="name" value="<?= getUser()->name; ?>">
                                                <span class="text-danger error-text name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="text" class="form-control" placeholder="Enter full Name" name="username" value="<?= getUser()->username; ?>">
                                                <span class="text-danger error-text username_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="bio">Bio</label>
                                        <textarea class="form-control" name="bio" placeholder="Enter your bio" cols="30" rows="10"><?= getUser()->bio; ?></textarea>
                                        <span class="text-danger error-text bio_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-sm">Update Profile</button>
                                    </div>
                               </form>
                            </div>
                        </div>
                        <!-- Personal Info Tab End -->

                        <!-- Change Password Tab start -->
                        <div class="tab-pane fade" id="change_password" role="tabpanel">
                            <div class="pd-20 profile-task-wrap">
                                ----------------- Change Password -----------------
                            </div>
                        </div>
                        <!-- Change Password Tab End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Handle the form submission for updating personal details
    $('#update-personal-details').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            dataType: 'json',
            contentType: false,
            beforeSend: function() {
                toastr.remove();
                $(form).find('span.error-text').text('');
            },
            success: function(response) {

                if ( $.isEmptyObject(response.error) ) {
                    if ( response.status == 1 ) {
                        $('.ci-user-name').each(function() {
                            $(this).html(response.user_info.name);
                        });
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                } else {
                    $.each(response.error, function(prefix, val) {
                        $(form).find('span.' + prefix + '_error').text(val);
                    });
                }
            }
        });
    });

    // Handle the file input for user profile picture
    $('#user_profile_file').ijaboCropTool({
        preview : '.ci-avatar-photo',
        setRatio:1,
        allowedExtensions: ['jpg', 'jpeg','png'],
        processUrl:'<?= route_to('update-profile-picture'); ?>',
        withCSRF:['<?= csrf_token() ?>','<?= csrf_hash() ?>'],
        onSuccess:function(message, element, status){
            if (status == 1) {
                toastr.success(message);
            } else {
                toastr.error(message);
            }
        },
        onError:function(message, element, status){
            alert(message);
        }
    });   

</script>
<?php $this->endsection(); ?>