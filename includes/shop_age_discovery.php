<?php // Existing large discovery cards, reused without changing their links. ?>
            <section class="agecat-section" id="age-cards" aria-labelledby="agecat-title">
                <div class="agecat-head">
                    <span class="agecat-eyebrow">Shop by age</span>
                    <h2 id="agecat-title"><?= htmlspecialchars($typeName) ?> for every age</h2>
                </div>

                <div class="agecat-grid">
                    <?php foreach ($ageCards as $key => $card): ?>
                        <?php $count = $toysByAge[$key]; ?>
                        <a class="agecat-card agecat-<?= $card['color'] ?>"
                           href="<?= htmlspecialchars(ageToysUrl($key, $categorySlug)) ?>">
                            <div class="agecat-top">
                                <span class="agecat-pic" aria-hidden="true"><?= $card['emoji'] ?></span>
                                <span class="agecat-range"><?= ageCardRange($card) ?> <small>years</small></span>
                            </div>
                            <div class="agecat-body">
                                <h3><?= htmlspecialchars($card['title']) ?></h3>
                                <p><?= htmlspecialchars($card['text']) ?></p>
                                <div class="agecat-foot">
                                    <span><?= $count ?> <?= $count === 1 ? 'toy' : 'toys' ?></span>
                                    <span class="agecat-go" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>