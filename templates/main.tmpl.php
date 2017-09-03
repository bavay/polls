<?php
    \OCP\Util::addStyle('polls', 'main');
    \OCP\Util::addStyle('polls', 'list');
    \OCP\Util::addScript('polls', 'start');
    use OCP\User;
    $userId = $_['userId'];
    $userMgr = $_['userMgr'];
    $urlGenerator = $_['urlGenerator'];
?>
<div id="app-navigation">
</div>


    <div id="app-content">
        <div id="app-content-wrapper">
                <div id="controls">
                    <h2><?php p($l->t('Summary')); ?></h>
                </div>
    <?php if(count($_['polls']) === 0) : ?>
        <?php p($l->t('No existing polls.')); ?>
    <?php else : ?>
            <table id="pollstable" class="has-controls">
                <thead>
                    <tr>
                        <th id="headerName" class="column-name">
                            <div id="headerName-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Title')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                        <th id="headerDescription" class="column-description">
                        <div id="headerDescription-container">
                            <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Description')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                                </div>
                        </th>

                        <th id="headerCreated" class="column-Created">
                            <div id="headerCreated-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Created')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>

                        <th id="headerPrincipal" class="column-Principal">
                            <div id="headerPrincipal-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('By')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                        
                        <th id="headerExpiry" class="column-Expiry">
                            <div id="headerExpiry-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Expires')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                        
                        <th id="headerVoted" class="column-Voted">
                            <div id="headerVoted-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('participated')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                        <th id="headerAccess" class="column-Access">
                            <div id="headerAccess-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Access')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                        <th id="headerOptions" class="column-Options">
                            <div id="headerOptions-container">
                                <a class="name sort columntitle" data-sort="name"><span><?php p($l->t('Options')); ?></span><span class="sort-indicator icon-triangle-n"></span></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="polllist">
                <?php foreach ($_['polls'] as $poll) : ?>
                    <?php
                        if (!userHasAccess($poll, $userId)) continue;
                        // direct url to poll
                        $pollUrl = $urlGenerator->linkToRoute('polls.page.goto_poll', array('hash' => $poll->getHash()));
                    ?>
                    <tr>
                        <td class="pollname">
                            <a class="name" href="<?php p($pollUrl); ?>">
                                <span class="nametext">
                                    <span class="innernametext">
                                        <?php p($poll->getTitle()); ?>
                                    </span>
                                </span>
                            </a>
                        </td>
                        <?php
                            $desc_str = $poll->getDescription();
                            if($desc_str === null) $desc_str = $l->t('No description provided.');
                            if (strlen($desc_str) > 100){
                                $desc_str = substr($desc_str, 0, 80) . '...';
                            }
                        ?>
                        <td class="description"><?php p($desc_str); ?></td>
                        <td class="created"><?php p(date('d.m.Y H:i', strtotime($poll->getCreated()))); ?></td>
                        <td class="principal">
                            <?php
                                if($poll->getOwner() === $userId) p($l->t('Yourself'));
                                else p($userMgr->get($poll->getOwner()));
                            ?>
                        </td>
                            <?php
                                if ($poll->getExpire() !== null) {
                                    $style = '';
                                    if (date('U') > strtotime($poll->getExpire())) {
                                        $style = 'expired';
                                    }
                                    print_unescaped('<td class="expiry ' . $style . '">' . date('d.m.Y', strtotime($poll->getExpire())) . '</td>');
                                }
                                else {
                                    print_unescaped('<td class="expiry">' . $l->t('Never') . '</td>');
                                }
                            ?>
                        <td class="expiry">
                            <?php
                                $partic_class = 'partic_no';
                                $partic_polls = $_['participations'];
                                for($i = 0; $i < count($partic_polls); $i++){
                                    if($poll->getId() == intval($partic_polls[$i]->getPollId())){
                                        $partic_class = 'partic_yes';
                                        array_splice($partic_polls, $i, 1);
                                        break;
                                    }
                                }
                            ?>
                            <div class="partic_all <?php p($partic_class); ?>">
                            </div>
                            |
                            <?php
                                $partic_class = 'partic_no';
                                $partic_comm = $_['comments'];
                                for($i = 0; $i < count($partic_comm); $i++){
                                    if($poll->getId() === intval($partic_comm[$i]->getPollId())){
                                        $partic_class = 'partic_yes';
                                        array_splice($partic_comm, $i, 1);
                                        break;
                                    }
                                }
                            ?>
                            <div class="partic_all <?php p($partic_class); ?>">
                            </div>
                        </td>
                        <td class="access">
                            <?php p($l->t($poll->getAccess())); ?>
                        </td>
                        <td class="actions">
                            <?php if ($poll->getOwner() === $userId) : ?>
                            <input type="button" id="id_del_<?php p($poll->getId()); ?>" class="table_button cl_delete icon-delete"></input>
                            <a href="<?php p($urlGenerator->linkToRoute('polls.page.edit_poll', ['hash' => $poll->getHash()])); ?>"><input type="button" id="id_edit_<?php p($poll->getId()); ?>" class="table_button cl_edit icon-rename"></input></a>
                            <?php endif; ?>
                            <input type="button" class="table_button cl_link icon-public" data-url="<?php p(OCP\Util::linkToAbsolute('', $pollUrl)); ?>" title="<?php p($l->t('Click to get link')); ?>"></input>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <form id="form_delete_poll" name="form_delete_poll" action="<?php p($urlGenerator->linkToRoute('polls.page.delete_poll')); ?>" method="POST">
            </form>
    <?php endif; ?>
            <a href="<?php p($urlGenerator->linkToRoute('polls.page.create_poll')); ?>"><input type="button" id="submit_new_poll" class="icon-add button btn" /></a>
            
        </div>
    </div>


<?php
// ---- helper functions ----
// from spreed.me
    function getGroups($userId) {
            // $this->requireLogin();
            if (class_exists('\OC_Group', true)) {
                    // Nextcloud <= 11, ownCloud
                    return \OC_Group::getUserGroups($userId);
            }
            // Nextcloud >= 12
            $groups = \OC::$server->getGroupManager()->getUserGroups(\OC::$server->getUserSession()->getUser());
            return array_map(function ($group) {
                    return $group->getGID();
            }, $groups);
    }

function userHasAccess($poll, $userId) {
    if($poll === null) return false;
    $access = $poll->getAccess();
    $owner = $poll->getOwner();
    if (!User::isLoggedIn()) return false;
    if ($access === 'public') return true;
    if ($access === 'hidden') return true;
    if ($access === 'registered') return true;
    if ($owner === $userId) return true;
    $user_groups = getGroups($userId);

    $arr = explode(';', $access);

    foreach ($arr as $item) {
        if (strpos($item, 'group_') === 0) {
            $grp = substr($item, 6);
            foreach ($user_groups as $user_group) {
                if ($user_group === $grp) return true;
            }
        }
        else if (strpos($item, 'user_') === 0) {
            $usr = substr($item, 5);
            if ($usr === $userId) return true;
        }
    }
    return false;
}
?>
