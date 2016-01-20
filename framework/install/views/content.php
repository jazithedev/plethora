<?php
/**
 * Main view of Plethora installation.
 *
 * @author     Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @package    install
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
?>

<?php /* @var $bPHPVersionCheck boolean */ ?>
<?php /* @var $bRegisterGlobals boolean */ ?>
<?php /* @var $bMeetsReqs boolean */ ?>
<?php /* @var $sAppName string */ ?>
<?php /* @var $aCached array */ ?>
<?php /* @var $bFilesPrepared boolean */ ?>
<?php /* @var $bDataBaseUpdated boolean */ ?>
<?php /* @var $bUserCreated boolean */ ?>

<?php
function getValueIfExists($aCached, $sKey, $sDefaultVal = '')
{
    return isset($aCached[$sKey]) ? $aCached[$sKey] : $sDefaultVal;
}

function disableTab($iLevel, $aCached)
{
    return (empty($aCached) || $aCached['step_done'] < $iLevel) ? 'disabled' : '';
}

?>

<h1>Plethora setup</h1>

<div id="step_done" class="hidden"><?= getValueIfExists($aCached, 'step_done', 0) ?></div>

<div class="container">
    <div class="row">
        <section>
            <div class="wizard">
                <?php if($bMeetsReqs): ?>
                    <div class="wizard-inner">
                        <div class="connecting-line"></div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Conditions">
									<span class="round-tab">
										<i class="glyphicon glyphicon-info-sign"></i>
									</span>
                                </a>
                            </li>
                            <li role="presentation" class="<?= disableTab(1, $aCached) ?>">
                                <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Basics">
									<span class="round-tab">
										<i class="glyphicon glyphicon-pencil"></i>
									</span>
                                </a>
                            </li>
                            <li role="presentation" class="<?= disableTab(2, $aCached) ?>">
                                <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Prepare files">
									<span class="round-tab">
										<i class="glyphicon glyphicon-road"></i>
									</span>
                                </a>
                            </li>
                            <li role="presentation" class="<?= disableTab(3, $aCached) ?>">
                                <a href="#step4" data-toggle="tab" aria-controls="step4" role="tab" title="Database update">
									<span class="round-tab">
										<i class="glyphicon glyphicon-hdd"></i>
									</span>
                                </a>
                            </li>
                            <li role="presentation" class="<?= disableTab(4, $aCached) ?>">
                                <a href="#step5" data-toggle="tab" aria-controls="step5" role="tab" title="Admin account">
									<span class="round-tab">
										<i class="glyphicon glyphicon-user"></i>
									</span>
                                </a>
                            </li>
                            <li role="presentation" class="<?= disableTab(5, $aCached) ?>">
                                <a href="#step6" data-toggle="tab" aria-controls="step6" role="tab" title="Complete">
									<span class="round-tab">
										<i class="glyphicon glyphicon-ok"></i>
									</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif ?>

                <div id="install_messages"></div>

                <div class="tab-content">
                    <section class="tab-pane active" role="tabpanel" id="step1">
                        <h2>Conditions</h2>
                        <p class="bg-info alert">Check all conditions of the framework to start installation.</p>
                        <div class="form-group">
                            <input type="checkbox" name="fancy-checkbox-" id="fancy-checkbox-success" autocomplete="off" checked="checked" disabled="disabled" />
                            <div class="btn-group">
                                <label for="fancy-checkbox-success" class="btn btn-<?= $bPHPVersionCheck ? 'success' : 'danger' ?>">
                                    <span class="glyphicon glyphicon-<?= $bPHPVersionCheck ? 'ok' : 'remove' ?>"></span>
                                    <span> </span>
                                </label>
                                <label for="fancy-checkbox-success" class="btn btn-default active">PHP version (min.
                                                                                                   5.4.12)</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="fancy-checkbox-" id="fancy-checkbox-success" autocomplete="off" checked="checked" disabled="disabled" />
                            <div class="btn-group">
                                <label for="fancy-checkbox-success" class="btn btn-<?= !$bRegisterGlobals ? 'success' : 'danger' ?>">
                                    <span class="glyphicon glyphicon-<?= !$bRegisterGlobals ? 'ok' : 'remove' ?>"></span>
                                    <span> </span>
                                </label>
                                <label for="fancy-checkbox-success" class="btn btn-default active">Register globals
                                                                                                   (Disabled)</label>
                            </div>
                        </div>
                        <?php if($bMeetsReqs): ?>
                            <ul class="list-inline pull-right">
                                <li>
                                    <button type="button" class="btn btn-primary next-step">Start setup</button>
                                </li>
                            </ul>
                        <?php endif ?>
                    </section>
                    <section class="tab-pane" role="tabpanel" id="step2">
                        <h2>Basics</h2>
                        <div class="bg-info alert">
                            <p>Fill out the following form fields to set basic data used in main Plethora config
                               files.</p>
                            <p>
                                <b>WARNING:</b> If you want to change the application workname, you need to change the
                                                name of an app directory located in
                                <i>public_html</i> directory.</p>
                        </div>
                        <form action="/" method="post" id="framework_install_prepare">
                            <input type="hidden" name="step" value="first_batch" />
                            <fieldset class="col-md-6">
                                <legend>Application</legend>
                                <div class="form-group">
                                    <label for="step1_app_wname">Application workname:</label>
                                    <input class="form-control" id="step1_app_wname" type="text" name="app_wname" disabled="disabled" value="<?= $sAppName ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_app_name">Application name:</label>
                                    <input class="form-control" id="step1_app_name" type="text" name="app_name" value="<?= getValueIfExists($aCached, 'app_name', 'My new app') ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_app_email">Application main e-mail:</label>
                                    <input class="form-control" id="step1_app_email" type="text" name="app_email" value="<?= getValueIfExists($aCached, 'app_email') ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_app_descr">Application description:</label>
                                    <textarea class="form-control" id="step1_app_descr" type="text" name="app_descr"><?= getValueIfExists($aCached, 'app_descr') ?></textarea>
                                </div>
                            </fieldset>
                            <fieldset class="col-md-6">
                                <legend>Database</legend>
                                <div class="form-group">
                                    <label for="step1_db_host">Host:</label>
                                    <input class="form-control" id="step1_db_host" type="text" name="db_host" value="<?= getValueIfExists($aCached, 'db_host', 'localhost') ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_db_name">Database name:</label>
                                    <input class="form-control" id="step1_db_name" type="text" name="app_db_name" value="<?= getValueIfExists($aCached, 'db_name') ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_db_uname">Database user name:</label>
                                    <input class="form-control" id="step1_db_uname" type="text" name="db_uname" value="<?= getValueIfExists($aCached, 'db_user') ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="step1_db_upassw">Database user password:</label>
                                    <input class="form-control" id="step1_db_upassw" type="password" name="db_upassw" value="" />
                                </div>
                            </fieldset>
                            <div class="form_actions col-md-12">
                                <?php if(empty($aCached) || $aCached['step_done'] <= 1): ?>
                                    <input type="submit" name="make_action" value="<?= empty($aCached) ? 'check data' : 'change data' ?>" class="btn btn-warning btn-lg" />
                                <?php else: ?>
                                    <div class="alert alert-danger">You can't modify these data here. If you want to do
                                                                    it anyway, edit appropriate config file.
                                    </div>
                                <?php endif ?>
                            </div>
                        </form>
                        <div class="first_batch_status"></div>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" class="btn btn-default prev-step">Previous step</button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-primary next-step" <?= empty($aCached) ? 'disabled="disabled"' : '' ?>>
                                    Next step
                                </button>
                            </li>
                        </ul>
                    </section>
                    <section class="tab-pane" role="tabpanel" id="step3">
                        <h2>Prepare files</h2>
                        <p class="bg-info alert">In this step, all application files will be copied to the proper place
                                                 with all of the data from the 2nd step.</p>
                        <form action="/" method="post" id="framework_files_copying">
                            <input type="hidden" name="step" value="prepare_files" />
                            <div class="form_actions col-md-12">
                                <?php if(!$bFilesPrepared): ?>
                                    <input type="submit" name="make_action" value="prepare application files" class="btn btn-warning btn-lg" />
                                <?php else: ?>
                                    <div class="alert alert-success">Files prepared.</div>
                                <?php endif ?>
                            </div>
                        </form>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" class="btn btn-default prev-step">Previous step</button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-primary btn-info-full next-step" <?= (!$bFilesPrepared) ? 'disabled="disabled"' : '' ?>>
                                    Next step
                                </button>
                            </li>
                        </ul>
                    </section>
                    <section class="tab-pane" role="tabpanel" id="step4">
                        <h2>Database update</h2>
                        <div class="bg-info alert">
                            <p>Click the submit button below to update database with all basic framework modules.</p>
                            <p><b>NOTICE:</b> it may take a while, so be patient ;).</p>
                        </div>
                        <form action="/" method="post" id="framework_update_database">
                            <input type="hidden" name="step" value="update_database" />
                            <div class="form_actions col-md-12">
                                <input type="submit" name="make_action" value="<?= $bDataBaseUpdated ? 'make update once more' : 'make update' ?>" class="btn btn-warning btn-lg" />
                            </div>
                        </form>
                        <div id="db_update-errors"></div>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" class="btn btn-default prev-step">Previous step</button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-primary btn-info-full next-step" <?= $bDataBaseUpdated ? '' : 'disabled="disabled"' ?>>
                                    Next step
                                </button>
                            </li>
                        </ul>
                    </section>
                    <section class="tab-pane" role="tabpanel" id="step5">
                        <h2>Admin account</h2>
                        <p class="bg-info alert">Create an admin account to get access to all functionalities on this
                                                 site.</p>
                        <?php if(!$bUserCreated): ?>
                            <form action="/" method="post" id="framework_account_install">
                                <input type="hidden" name="step" value="create_user" />
                                <fieldset>
                                    <div class="form-group">
                                        <label for="user_name">Admin username:</label>
                                        <input class="form-control" id="user_name" type="text" name="user_name" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="user_email">Admin e-mail:</label>
                                        <input class="form-control" id="user_email" type="text" name="user_email" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="user_pass">Admin password:</label>
                                        <input class="form-control" id="user_pass" type="password" name="user_pass" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="user_pass2">Confirm admin password:</label>
                                        <input class="form-control" id="user_pass2" type="password" name="user_pass2" value="" />
                                    </div>
                                </fieldset>
                                <div class="form_actions col-md-12">
                                    <input type="submit" name="make_action" value="create user" class="btn btn-warning btn-lg" />
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success">User created.</div>
                        <?php endif ?>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" class="btn btn-default prev-step">Previous step</button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-primary btn-info-full next-step" <?= $bUserCreated ? '' : 'disabled="disabled"' ?>>
                                    Next step
                                </button>
                            </li>
                        </ul>
                    </section>
                    <section class="tab-pane" role="tabpanel" id="step6">
                        <h2>Complete</h2>
                        <div class="bg-info alert">
                            <p>Your application has been installed. Click the button below to finish the setup process
                               and to start using our framework!</p>
                            <p style="font-weight: bold;">NOTE:</p>
                            <p>If you would need to use
                                <b>Plethora setup</b> again to install other application, make further steps:</p>
                            <ol>
                                <li>Create new directory in the <code>/public_html/</code> (name of this directory
                                    will be a work name of this application).
                                </li>
                                <li>Copy all files (except for the following directories: <code>temp</code>,
                                    <code>thumbs</code>, <code>uploads</code>) from current aplication public files
                                    (<code>/public_html/<?= $sAppName ?>/</code>) to newly created directory.
                                </li>
                                <li>Copy <code>/framework/install/install.php</code> file to newly created directory.
                                </li>
                            </ol>
                        </div>

                        <form action="/" method="post" id="framework_setup_end">
                            <input type="hidden" name="step" value="end" />
                            <div class="form_actions col-md-12">
                                <input type="submit" name="make_action" value="clear temp files and finish this setup" class="btn btn-warning btn-lg" />
                            </div>
                        </form>
                    </section>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    var FwInstall = {
        sAppName: '',
        $InstallMessages: $('div#install_messages'),
        resetMessageContainer: function() {
            this.$InstallMessages
                .text('')
                .removeAttr('class');
        },
        setMessage: function(msg, status) {
            this.resetMessageContainer();

            if(typeof status === 'undefined') {
                status = 'success';
            }

            if(status === 'error') {
                status = 'danger';
            }

            this.$InstallMessages.addClass('alert alert-' + status);
            this.$InstallMessages.text(msg);
        },
        unblockNextStepButton: function($Form) {
            $Form
                .closest('section')
                .find('button.next-step')
                .prop('disabled', false);
        }
    };

    $(function() {
        // go to earlier clicked tab
        if(getLastStepNumber() !== '') {
            var $tab = $('div.wizard ul.nav-tabs a[href=#step' + getLastStepNumber() + ']');

            $tab.parent('li').removeClass('disabled');
            $tab.click();
        }

        // FORMS
        $('#framework_install_prepare').submit(function(e) {
            e.preventDefault();

            var $this    = $(this);
            var params   = {};
            var $submit  = $this.find('input[name=make_action]');
            var sOldText = $submit.val();

            $submit.val('loading...').prop('disabled', true);

            $this.serializeArray().map(function(x) {
                params[x.name] = x.value;
            });

            $.ajax({
                data: params, type: 'POST', dataType: 'json'
            }).success('/', function(output) {
                FwInstall.setMessage(output.msg, output.status);

                $submit.val(sOldText).prop('disabled', false);

                if(output.status === 'success') {
                    $submit.val('data checked');
                    $submit.addClass('btn-success');
                    $submit.removeClass('btn-warning');
                    $submit.prop('disabled', true);

                    $('input[name=app_wname]').prop('disabled', true);
                    FwInstall.unblockNextStepButton($this);
                }
            });
        });

        // COPYING FILES
        $('#framework_files_copying').submit(function(e) {
            e.preventDefault();

            var $this    = $(this);
            var params   = {};
            var $submit  = $this.find('input[name=make_action]');
            var sOldText = $submit.val();

            $submit.val('loading...').prop('disabled', true);

            $this.serializeArray().map(function(x) {
                params[x.name] = x.value;
            });

            params['app_name'] = $('#step1_app_wname').val();

            $.ajax({
                data: params, type: 'POST', dataType: 'json'
            }).success('/', function(output) {
                FwInstall.setMessage(output.msg, output.status);

                $submit.val(sOldText).prop('disabled', false);

                if(output.status === 'refresh') {
                    location.reload();
                }
                else if(output.status === 'success') {
                    $submit.val('DONE');
                    $submit.addClass('btn-success');
                    $submit.removeClass('btn-warning');
                    $submit.prop('disabled', true);

                    FwInstall.unblockNextStepButton($this);
                }
            });
        });

        // DATABASE UPDATE
        $('#framework_update_database').submit(function(e) {
            e.preventDefault();

            var $this   = $(this);
            var params  = {};
            var $submit = $this.find('input[name=make_action]');
            var oldText = $submit.val();
            var loader  = $('<img class="ajax-loader" src="/themes/backend/images/ajax-loader.gif" style="margin-left: 10px;">');

            $('div#db_update-errors').html('');

            $submit.val('loading...').prop('disabled', true);
            $submit.after(loader);

            $this.serializeArray().map(function(x) {
                params[x.name] = x.value;
            });

            params['app_name'] = $('#step1_app_wname').val();

            $.ajax({
                data: params, type: 'POST', dataType: 'json'
            }).always(function() {
                $submit.val(oldText).prop('disabled', false);
                loader.remove();
            }).success('/', function(output) {
                FwInstall.setMessage(output.msg, output.status);

                if(output.status === 'success') {
                    $submit.val('DONE').addClass('btn-success').removeClass('btn-warning').prop('disabled', true);
                    FwInstall.unblockNextStepButton($this);
                }
            }).fail(function(output) {
                $('div#db_update-errors').html(output.responseText);
            });
        });

        // ADMIN ACCOUNT CREATION
        $('#framework_account_install').submit(function(e) {
            e.preventDefault();

            var $this   = $(this);
            var params  = {};
            var $submit = $this.find('input[name=make_action]');
            var oldText = $submit.val();

            $submit.val('loading...');
            $submit.prop('disabled', true);

            $this.serializeArray().map(function(x) {
                params[x.name] = x.value;
            });

            params['app_name'] = $('#step1_app_wname').val();

            $.ajax({
                data: params, type: 'POST', dataType: 'json'
            }).success('/', function(output) {
                FwInstall.setMessage(output.msg, output.status);

                $submit.val(oldText);
                $submit.prop('disabled', false);

                if(output.status === 'success') {
                    $submit.val('DONE');
                    $submit.addClass('btn-success');
                    $submit.removeClass('btn-warning');
                    $submit.prop('disabled', true);

                    FwInstall.unblockNextStepButton($this);
                }
            });
        });

        // FINAL STEP
        $('#framework_setup_end').submit(function(e) {
            e.preventDefault();

            var $this   = $(this);
            var params  = {};
            var $submit = $this.find('input[name=make_action]');

            $submit.val('loading...').prop('disabled', true);

            $this.serializeArray().map(function(x) {
                params[x.name] = x.value;
            });

            params['app_name'] = $('#step1_app_wname').val();

            $.ajax({
                data: params, type: 'POST', dataType: 'json'
            }).success('/', function() {
                location.reload();
            });
        });

        // initialize tooltips
        $('.nav-tabs > li a[title]').tooltip();
        $('.nav-tabs > li a').click(function() {
            FwInstall.resetMessageContainer();
        });

        // wizard
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var $target = $(e.target);

            if($target.parent().hasClass('disabled')) {
                return false;
            }

            saveStepNumber($(this));
        });

        $('.next-step').click(function(e) {
            $('.wizard .nav-tabs li.active').next().removeClass('disabled');

            FwInstall.resetMessageContainer();

            nextTab();
        });

        $('.prev-step').click(function(e) {
            FwInstall.resetMessageContainer();
            prevTab();
        });

        $('input.btn').click(function() {
            FwInstall.resetMessageContainer();
        });
    });

    function nextTab() {
        $('.wizard .nav-tabs li.active').next().find('a[data-toggle="tab"]').click();
    }

    function prevTab() {
        $('.wizard .nav-tabs li.active').prev().find('a[data-toggle="tab"]').click();
    }

    // last step to cookie
    function saveStepNumber($tab) {
        setCookie('install-last_step', parseInt($tab.attr('href').replace('#step', '')));
    }

    function getLastStepNumber() {
        return getCookie('install-last_step');
    }

    // cookies
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();

        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca   = document.cookie.split(';');

        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];

            while(c.charAt(0) == ' ') {
                c = c.substring(1);
            }

            if(c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }

        return "";
    }
</script>