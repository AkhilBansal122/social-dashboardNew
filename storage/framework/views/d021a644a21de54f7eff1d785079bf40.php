<div>
<style>
/* ── Page header ── */
.ph { margin-bottom: 2rem; }
.ph h2 { font-size: 1.5rem; font-weight: 700; letter-spacing: -.02em; }
.ph p  { color: var(--muted); font-size: .875rem; margin-top: .3rem; }

/* ── Demo banner ── */
.demo-banner {
    background: rgba(124,111,247,.09); border: 1px solid rgba(124,111,247,.25);
    border-radius: var(--r2); padding: .875rem 1.1rem;
    display: flex; align-items: flex-start; gap: .75rem;
    margin-bottom: 1.75rem; font-size: .875rem; color: #c4b5fd; line-height: 1.55;
}
.demo-banner strong { color: #a78bfa; }

/* ── Flash ── */
.flash { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem 1.1rem; border-radius: var(--r2); margin-bottom: 1.5rem; font-size: .875rem; }
.flash-s { background: rgba(34,197,94,.08);  border: 1px solid rgba(34,197,94,.2);  color: #86efac; }
.flash-e { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.2);  color: #fca5a5; }
.flash-x { background: none; border: none; color: inherit; cursor: pointer; font-size: 1.1rem; opacity: .65; flex-shrink: 0; }
.flash-x:hover { opacity: 1; }

/* ── Section label ── */
.sec-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); margin-bottom: .875rem; }

/* ── Platform grid ── */
.plat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(295px, 1fr)); gap: 1.1rem; margin-bottom: 2.5rem; }

/* ── Platform card ── */
.plat-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r); overflow: hidden; transition: border-color .2s; }
.plat-card:hover { border-color: #3a3a52; }

.plat-head { padding: .9rem 1.1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
.plat-head.ig   { background: linear-gradient(135deg, rgba(240,148,51,.12), rgba(188,24,136,.12)); border-bottom-color: rgba(220,39,67,.15); }
.plat-head.snap { background: rgba(255,252,0,.06); border-bottom-color: rgba(255,252,0,.12); }
.plat-head.empty { background: var(--bg3); }

.plat-name { display: flex; align-items: center; gap: .6rem; font-weight: 600; font-size: .9rem; }
.plat-icon { width: 30px; height: 30px; border-radius: 7px; display: grid; place-items: center; font-size: .95rem; flex-shrink: 0; }
.plat-icon.ig   { background: linear-gradient(135deg, #f09433, #dc2743, #bc1888); }
.plat-icon.snap { background: var(--snap); }
.plat-icon.empty { background: var(--bg4); border: 1px dashed var(--border); }

/* ── Profile inside card ── */
.profile-row { display: flex; align-items: center; gap: .85rem; margin-bottom: 1rem; }
.avatar { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background: var(--bg4); }
.avatar-ph { width: 46px; height: 46px; border-radius: 50%; background: var(--bg4); border: 2px dashed var(--border); display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0; }
.profile-meta { min-width: 0; }
.profile-name   { font-weight: 600; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.profile-handle { font-size: .78rem; color: var(--muted); }
.profile-type   { font-size: .73rem; color: var(--muted); margin-top: .1rem; }

/* ── Card body ── */
.plat-body { padding: 1.1rem; }
.card-msg { font-size: .8rem; padding: .55rem .8rem; border-radius: var(--r3); margin-bottom: .85rem; }
.msg-s { background: rgba(34,197,94,.08);  border: 1px solid rgba(34,197,94,.2);  color: #86efac; }
.msg-e { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.2);  color: #fca5a5; }
.msg-w { background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.2); color: #fde68a; }
.msg-i { background: rgba(59,130,246,.08); border: 1px solid rgba(59,130,246,.2); color: #93c5fd; }

.actions { display: flex; flex-wrap: wrap; gap: .45rem; }
.sync-meta { font-size: .72rem; color: var(--muted); margin-top: .7rem; }

/* ── Not connected ── */
.empty-card { text-align: center; padding: 1.75rem 1.1rem; }
.empty-card p { color: var(--muted); font-size: .875rem; margin-bottom: 1rem; line-height: 1.5; }

/* ── Media section ── */
.media-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.1rem; }
.media-title { font-size: 1rem; font-weight: 600; }
.media-count { font-size: .78rem; color: var(--muted); }
.media-controls { margin-left: auto; display: flex; gap: .4rem; align-items: center; }
.view-btn { padding: .3rem .65rem; border-radius: var(--r3); border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; font-size: .8rem; font-family: inherit; transition: all .18s; }
.view-btn.on { background: var(--primary); border-color: var(--primary); color: #fff; }
.per-select { background: var(--bg3); border: 1px solid var(--border); border-radius: var(--r3); color: var(--muted); font-size: .8rem; padding: .28rem .5rem; font-family: inherit; cursor: pointer; }

/* ── Media grid ── */
.mgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(185px, 1fr)); gap: .875rem; }
.mcard { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r2); overflow: hidden; transition: transform .2s, border-color .2s; cursor: pointer; }
.mcard:hover { transform: translateY(-2px); border-color: var(--primary); }
.mthumb-wrap { position: relative; aspect-ratio: 1; background: var(--bg4); overflow: hidden; }
.mthumb { width: 100%; height: 100%; object-fit: cover; display: block; }
.mthumb-ph { width: 100%; height: 100%; display: grid; place-items: center; color: var(--muted); font-size: 2.25rem; }
.mtype { position: absolute; top: .45rem; right: .45rem; background: rgba(0,0,0,.65); border-radius: 4px; padding: .1rem .35rem; font-size: .72rem; backdrop-filter: blur(4px); }
.minfo { padding: .7rem .8rem; }
.mcap { font-size: .78rem; color: var(--muted); line-height: 1.4; min-height: 2.4em; margin-bottom: .45rem; }
.mstats { display: flex; gap: .65rem; flex-wrap: wrap; font-size: .73rem; color: var(--muted); }
.mstat { display: flex; align-items: center; gap: .2rem; }
.mdate { font-size: .69rem; color: var(--muted); margin-top: .35rem; opacity: .75; }

/* ── Media list ── */
.mlist { display: flex; flex-direction: column; gap: .65rem; }
.mrow { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r2); padding: .875rem; display: flex; gap: .875rem; align-items: flex-start; transition: border-color .18s; }
.mrow:hover { border-color: var(--primary); }
.mrow-thumb { width: 60px; height: 60px; border-radius: var(--r3); object-fit: cover; flex-shrink: 0; background: var(--bg4); }
.mrow-thumb-ph { width: 60px; height: 60px; border-radius: var(--r3); background: var(--bg4); display: grid; place-items: center; color: var(--muted); font-size: 1.3rem; flex-shrink: 0; }
.mrow-body { flex: 1; min-width: 0; }
.mrow-cap { font-size: .85rem; line-height: 1.45; margin-bottom: .35rem; }
.mrow-meta { display: flex; flex-wrap: wrap; gap: .75rem; font-size: .78rem; color: var(--muted); }
.mrow-date { font-size: .75rem; color: var(--muted); white-space: nowrap; margin-left: auto; }

/* ── Insight badge ── */
.ins-badge { background: rgba(124,111,247,.12); color: #a78bfa; border: 1px solid rgba(124,111,247,.25); border-radius: 99px; padding: .1rem .45rem; font-size: .7rem; }

/* ── Pagination ── */
.pager { display: flex; justify-content: center; margin-top: 1.5rem; }
.pager-inner { display: flex; gap: .35rem; flex-wrap: wrap; justify-content: center; }
.pager-btn { padding: .35rem .7rem; border-radius: var(--r3); border: 1px solid var(--border); background: var(--bg2); color: var(--muted); cursor: pointer; font-size: .82rem; font-family: inherit; transition: all .18s; text-decoration: none; display: inline-block; }
.pager-btn:hover { border-color: var(--primary); color: var(--text); }
.pager-btn.current { background: var(--primary); border-color: var(--primary); color: #fff; }
.pager-btn.disabled { opacity: .35; pointer-events: none; }

/* ── Empty state ── */
.empty { text-align: center; padding: 3.5rem 2rem; color: var(--muted); }
.empty-ico { font-size: 2.75rem; margin-bottom: .875rem; }
.empty h3 { font-size: 1rem; font-weight: 600; color: var(--text); margin-bottom: .4rem; }
.empty p { font-size: .875rem; }

/* ── Loading skeleton ── */
.skel { background: linear-gradient(90deg, var(--bg3) 25%, var(--bg4) 50%, var(--bg3) 75%); background-size: 200%; animation: shim 1.4s infinite; border-radius: var(--r3); }
@keyframes shim { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>

<div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="flash flash-s">
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="flash flash-e">
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->demoMode): ?>
        <div class="demo-banner">
            <span style="font-size:1.2rem;flex-shrink:0">🎯</span>
            <span>
                <strong>Demo Mode is active.</strong>
                The accounts below are pre-seeded with fake data.
                To connect real accounts, set <code style="background:rgba(124,111,247,.18);padding:.1rem .35rem;border-radius:4px;font-size:.9em">SOCIAL_DEMO_MODE=false</code>
                and add your API credentials in <code style="background:rgba(124,111,247,.18);padding:.1rem .35rem;border-radius:4px;font-size:.9em">.env</code>.
            </span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="ph">
        <h2>Your Social Accounts</h2>
        <p>Connect, sync and manage Instagram and Snapchat from one place.</p>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat => $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flash <?php echo e($msg['type'] === 'success' ? 'flash-s' : 'flash-e'); ?>" style="margin-bottom:.75rem">
            <span><?php echo e($msg['text']); ?></span>
            <button class="flash-x" wire:click="clearMessage('<?php echo e($plat); ?>')">✕</button>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="sec-label">Connected platforms</div>
    <div class="plat-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $account = $this->connectedAccounts->get($platform); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account): ?>
                <div class="plat-card">
                    
                    <div class="plat-head <?php echo e($platform === 'instagram' ? 'ig' : 'snap'); ?>">
                        <div class="plat-name">
                            <div class="plat-icon <?php echo e($platform === 'instagram' ? 'ig' : 'snap'); ?>">
                                <?php echo e($platform === 'instagram' ? '📸' : '👻'); ?>

                            </div>
                            <?php echo e($account->platformLabel()); ?>

                        </div>
                        <span class="badge <?php echo e(match($account->status) { 'active' => 'badge-green', 'expired' => 'badge-amber', 'error' => 'badge-red', default => 'badge-muted' }); ?>">
                            <span class="dot"></span><?php echo e($account->statusLabel()); ?>

                        </span>
                    </div>

                    
                    <div class="plat-body">

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->status === 'expired'): ?>
                            <div class="card-msg msg-w">
                                ⚠ <?php echo e($account->status_message ?? 'Token expired — please reconnect.'); ?>

                            </div>
                        <?php elseif($account->status === 'error'): ?>
                            <div class="card-msg msg-e">
                                ✗ <?php echo e($account->status_message ?? 'An error occurred.'); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->profile): ?>
                            <div class="profile-row">
                                <img src="<?php echo e($account->profile->getAvatarUrl()); ?>"
                                     alt="avatar"
                                     class="avatar"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
                                <div class="avatar-ph" style="display:none">👤</div>
                                <div class="profile-meta">
                                    <div class="profile-name"><?php echo e($account->profile->display_name ?? $account->profile->username ?? 'Unknown'); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->profile->username): ?>
                                        <div class="profile-handle"><?php echo e($account->profile->username); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->profile->account_type): ?>
                                        <div class="profile-type"><?php echo e(ucwords(strtolower(str_replace('_', ' ', $account->profile->account_type)))); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="font-size:.82rem;color:var(--muted);margin-bottom:.9rem">Profile will appear after the first sync.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="actions">
                            <?php $syncing = $platform === 'instagram' ? $syncingInstagram : $syncingSnapchat; ?>

                            <button class="btn btn-primary btn-sm"
                                    wire:click="syncPlatform('<?php echo e($platform); ?>')"
                                    <?php if($account->status === 'expired' || $syncing): ?> disabled <?php endif; ?>>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($syncing): ?>
                                    <span class="spin"></span> Syncing…
                                <?php else: ?>
                                    🔄 Sync Now
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </button>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($account->status, ['expired', 'error'])): ?>
                                <a href="/auth/<?php echo e($platform); ?>"
                                   class="btn btn-sm <?php echo e($platform === 'instagram' ? 'btn-ig' : 'btn-snap'); ?>">
                                    🔗 Reconnect
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <button class="btn btn-danger btn-sm"
                                    wire:click="disconnect('<?php echo e($platform); ?>')"
                                    wire:confirm="Disconnect <?php echo e($account->platformLabel()); ?>? This will remove all synced data for this account.">
                                ✕ Disconnect
                            </button>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->last_synced_at): ?>
                            <div class="sync-meta">Last synced <?php echo e($account->last_synced_at->diffForHumans()); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                
                <div class="plat-card">
                    <div class="plat-head empty">
                        <div class="plat-name">
                            <div class="plat-icon empty">
                                <?php echo e($platform === 'instagram' ? '📸' : '👻'); ?>

                            </div>
                            <?php echo e(ucfirst($platform)); ?>

                        </div>
                        <span class="badge badge-muted"><span class="dot"></span>Not connected</span>
                    </div>
                    <div class="plat-body">
                        <div class="empty-card">
                            <p>Connect your <?php echo e(ucfirst($platform)); ?> account to start syncing your content and profile data.</p>
                            <a href="/auth/<?php echo e($platform); ?>"
                               class="btn <?php echo e($platform === 'instagram' ? 'btn-ig' : 'btn-snap'); ?>">
                                🔗 Connect <?php echo e(ucfirst($platform)); ?>

                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->instagramAccount): ?>
        <div class="divider"></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->instagramAccount->isActive()): ?>
            <div class="media-header">
                <div>
                    <div class="media-title">📸 Instagram Posts</div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->instagramMedia && $this->instagramMedia->total() > 0): ?>
                        <div class="media-count"><?php echo e(number_format($this->instagramMedia->total())); ?> posts synced</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="media-controls">
                    <button class="view-btn <?php echo e($mediaView === 'grid' ? 'on' : ''); ?>" wire:click="setView('grid')">⊞ Grid</button>
                    <button class="view-btn <?php echo e($mediaView === 'list' ? 'on' : ''); ?>" wire:click="setView('list')">☰ List</button>
                    <select class="per-select" wire:change="setPerPage($event.target.value)">
                        <option value="12"  <?php echo e($perPage == 12  ? 'selected' : ''); ?>>12 / page</option>
                        <option value="24"  <?php echo e($perPage == 24  ? 'selected' : ''); ?>>24 / page</option>
                        <option value="48"  <?php echo e($perPage == 48  ? 'selected' : ''); ?>>48 / page</option>
                    </select>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->instagramMedia && $this->instagramMedia->count() > 0): ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mediaView === 'grid'): ?>
                    <div class="mgrid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->instagramMedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mcard">
                                <div class="mthumb-wrap">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->getThumbnail()): ?>
                                        <img src="<?php echo e($m->getThumbnail()); ?>" alt="" class="mthumb"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
                                        <div class="mthumb-ph" style="display:none">🖼️</div>
                                    <?php else: ?>
                                        <div class="mthumb-ph">🎥</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="mtype"><?php echo e($m->mediaTypeIcon()); ?></span>
                                </div>
                                <div class="minfo">
                                    <div class="mcap"><?php echo e($m->getCaptionSnippet(85) ?: '—'); ?></div>
                                    <div class="mstats">
                                        <span class="mstat">❤️ <?php echo e(number_format($m->like_count)); ?></span>
                                        <span class="mstat">💬 <?php echo e(number_format($m->comments_count)); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->hasInsights()): ?>
                                            <span class="ins-badge">👁 <?php echo e(number_format($m->impressions)); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->posted_at): ?>
                                        <div class="mdate"><?php echo e($m->posted_at->format('M j, Y')); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="mlist">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->instagramMedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mrow">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->getThumbnail()): ?>
                                    <img src="<?php echo e($m->getThumbnail()); ?>" alt="" class="mrow-thumb"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='grid'">
                                    <div class="mrow-thumb-ph" style="display:none">🖼️</div>
                                <?php else: ?>
                                    <div class="mrow-thumb-ph">🎥</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mrow-body">
                                    <div class="mrow-cap"><?php echo e($m->getCaptionSnippet(160) ?: '(no caption)'); ?></div>
                                    <div class="mrow-meta">
                                        <span><?php echo e($m->mediaTypeIcon()); ?> <?php echo e(str_replace('_', ' ', $m->media_type)); ?></span>
                                        <span>❤️ <?php echo e(number_format($m->like_count)); ?></span>
                                        <span>💬 <?php echo e(number_format($m->comments_count)); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->hasInsights()): ?>
                                            <span class="ins-badge">👁 <?php echo e(number_format($m->reach)); ?> reach · <?php echo e(number_format($m->impressions)); ?> imp.</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="mrow-date"><?php echo e($m->posted_at?->format('M j, Y')); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="pager">
                    <div class="pager-inner">
                        <?php echo e($this->instagramMedia->links('livewire.pagination')); ?>

                    </div>
                </div>

            <?php else: ?>
                <div class="empty">
                    <div class="empty-ico">📭</div>
                    <h3>No posts yet</h3>
                    <p>Click "Sync Now" on your Instagram card to fetch your posts.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php elseif($this->instagramAccount->status === 'expired'): ?>
            <div class="empty">
                <div class="empty-ico">⏰</div>
                <h3>Instagram token expired</h3>
                <p><?php echo e($this->instagramAccount->status_message ?? 'Your Instagram session has expired.'); ?></p>
                <div style="margin-top:1rem">
                    <a href="/auth/instagram" class="btn btn-ig">🔗 Reconnect Instagram</a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty">
                <div class="empty-ico">⚠️</div>
                <h3>Instagram needs attention</h3>
                <p><?php echo e($this->instagramAccount->status_message ?? 'Please reconnect your Instagram account.'); ?></p>
                <div style="margin-top:1rem">
                    <a href="/auth/instagram" class="btn btn-ig">🔗 Reconnect Instagram</a>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
</div>
<?php /**PATH C:\laragon\www\social-dashboard-v2\resources\views/livewire/dashboard.blade.php ENDPATH**/ ?>