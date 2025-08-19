<?= $this->extend('backend/layout/pages-layout'); ?>
<?= $this->section('content'); ?>

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Settings</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('admin.home'); ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Settings
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="pd-20 card-box mb-4">
    <div class="tab">
        <ul class="nav nav-tabs customtab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#general_settings" role="tab" aria-selected="true">General Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#logo_favicon" role="tab" aria-selected="false">Logo & Favicon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#social_media" role="tab" aria-selected="false">Social Media</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="general_settings" role="tabpanel">
                <div class="pd-20"> 
                    <form action="<?= route_to('update-general-settings'); ?>" method="post" id="general-settings-form">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="ci_csrf_data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website_title">Website Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Website Title" name="website_title" value="<?= getSettings()->website_title; ?>">
                                    <span class="text-danger error-text website_title_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website_email">Website Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Website Email" name="website_email" value="<?= getSettings()->website_email; ?>">
                                    <span class="text-danger error-text website_email_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website_phone">Website Phone No.</label>
                                    <input type="text" class="form-control" placeholder="Enter Website Phone No." name="website_phone" value="<?= getSettings()->website_phone; ?>">
                                    <span class="text-danger error-text website_phone_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website_meta_keywords">Website Meta Keywords</label>
                                    <input type="text" class="form-control" placeholder="Enter Website Meta keywords" name="website_meta_keywords" value="<?= getSettings()->website_meta_keywords; ?>">
                                    <span class="text-danger error-text website_meta_keywords_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="website_meta_description">Website Meta Description</label>
                            <textarea name="website_meta_description" id="" cols="4" rows="3" class="form-control" placeholder="Enter Website Meta Description"><?= getSettings()->website_meta_description; ?></textarea>
                            <span class="text-danger error-text website_meta_description_error"></span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="logo_favicon" role="tabpanel">
                <div class="pd-20"> 
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Set Logo</h5>
                            <div class="mb2 mt-1" style="max-width: 200px;">
                                <img src="" alt="" class="img-thumbnail" id="logo-image-preview" data-ijabo-default-img="/images/logo/<?= getSettings()->website_logo; ?>">
                            </div>
                            <form action="<?= route_to('update-website-logo'); ?>" method="post" enctype="multipart/form-data" id="changeWebsiteLogoForm">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="ci_csrf_data">

                                <div class="mb-2">
                                    <input type="file" name="website_logo" id="website_logo" class="form-control">
                                    <span class="text-danger error-text website_logo_error"></span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Change Logo</button>
                            </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="social_media" role="tabpanel">
                <div class="pd-20"> 
                    ---------- Social Media Content ----------
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>


<?= $this->section('scripts'); ?>
<script>
    // Handle the form submission for updating General Setting details
    $('#general-settings-form').on('submit', function(e) {
        e.preventDefault();

         // CSRF Hash
        var csrfName = $('.ci_csrf_data').attr('name'); // CSRF token name
        var csrfHash = $('.ci_csrf_data').val(); // CSRF hash value
        var form = this;
        var formData = new FormData(form);
        formData.append(csrfName, csrfHash); // Append CSRF token to the form data
        
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            dataType: 'json',
            contentType: false,
            cache: false,
            beforeSend: function() {
                toastr.remove();
                $(form).find('span.error-text').text('');
            },
            success: function(response) {

                // Update CSRF token value
                $('.ci_csrf_data').val(response.token); 

                if ( $.isEmptyObject(response.error) ) {
                    if ( response.status == 1 ) {
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

    // Handle the file input for Logo
    $('input[type="file"][name="website_logo"]').ijaboViewer({
        preview : '#logo-image-preview',
        // imageShape: 'rectangular', // or square if it is a avatar
        allowedExtensions: ['jpg', 'jpeg','png', 'svg'],
        /* processUrl:'<?= route_to('update-profile-picture'); ?>',
        withCSRF:['<?= csrf_token() ?>','<?= csrf_hash() ?>'], */
        onErrorShape: function(message, element){
            alert(message);
        },
        onInvalidType: function(message, element){
            alert(message);
        },
        onSuccess:function(message, element){

        }
    });   


    // Handle the form submission for changing password
    $('#changeWebsiteLogoForm').on('submit', function(e) {
        e.preventDefault();

        /* // CSRF Hash
        var csrfName = $('.ci_csrf_data').attr('name'); // CSRF token name
        var csrfHash = $('.ci_csrf_data').val();
        var form = this;
        var formData = new FormData(form);
        formData.append(csrfName, csrfHash); // Append CSRF token to the form data
        
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: formData,
            processData: false,
            dataType: 'json',
            contentType: false,
            cache: false,
            beforeSend: function() {
                toastr.remove();
                $(form).find('span.error-text').text('');
            },
            success: function(response) {

                // Update CSRF token value
                $('.ci_csrf_data').val(response.token); 

                if ( $.isEmptyObject(response.error) ) {
                    if ( response.status == 1 ) {
                        $(form)[0].reset(); // Reset the form
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
        }); */
    });
</script>
<?php $this->endsection(); ?>