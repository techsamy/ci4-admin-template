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
                <a href="modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                <?php 
                 $avatar = getUser()->picture 
                    ? base_url('/images/users/' . getUser()->picture) 
                    : base_url('/images/users/default-avatar.png');

                    var_dump($avatar);
                ?>
                <img src="<?= $avatar ?>" alt="" class="avatar-photo ci-avatar-photo" />
            </div>
            <h5 class="text-center h5 mb-0"><?= getUser()->name; ?></h5>
            <p class="text-center text-muted font-14">
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
                               --------- Personal Info ------------------
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