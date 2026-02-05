<?php

declare(strict_types=1);

if (empty($args['items'])) {
    return;
}

// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
//print_r($args);
$name = $args['name'] ?? '';
$title = $args['title'] ?? '';
$items = $args['items'] ?? [];
$limit = $args['limit'] ?? '';
$activeItems = $args['activeItems'] ?? [];
if (!is_array($activeItems)) {
    $activeItems = array_map('trim', explode(',', $activeItems));
}

$hideSearch = $args['hideSearch'] ?? false;
$prefix = $args['prefix'] ?? '';
$postfix = $args['postfix'] ?? '';
// phpcs:enable

$wrapperClass = 'bs-filter bs-filter--multiple bs-filter--' . $name;
?>

<div class="<?php echo esc_attr($wrapperClass); ?>" data-name="<?php echo esc_attr($name); ?>" data-items="<?php echo esc_attr(implode(',', $items)); ?>" data-active-items="<?php echo esc_attr(implode(',', $activeItems)); ?>" data-limit="<?php echo $limit; ?>">
    <?php if (!empty($title)) : ?>
        <h3 class="bs-filter__title">
            <?php echo esc_html($title); ?>
            <span class="bs-filter__collapse"></span>
        </h3>
    <?php endif; ?>

    <div class="bs-filter__body">
        <?php if (count($items) > $limit && !$hideSearch) : ?>
        <div class="bs-filter__search">
            <input type="text" class="bs-filter__search-box" placeholder="Search...">
            <div class="bs-filter__search-results"></div>
        </div>
        <?php endif; ?>

        <?php if (!empty($activeItems)) : ?>
        <div class="bs-filter__active-items">
            <?php foreach ($activeItems as $activeItem) : ?>
            <span class="bs-filter__active-item dk-bg--white dk-color--black" data-value="<?php echo esc_attr($activeItem); ?>">
                <span class="bs-filter__active-item-text"><?php echo esc_html($prefix . $activeItem . $postfix); ?></span>
                <button class="bs-filter__active-item-remove dk-bg--white dk-color--black">x</button>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <ul class="bs-filter__items <?php echo ( count($items) > $limit && !$hideSearch ) ? 'bs-filter__body-scroll' : ''; ?>">
        <?php
        foreach ($items as $index => $item) {
            $isActive = in_array($item, $activeItems, true);
            $itemClass = sprintf(
                'bs-filter__item bs-filter__%s_%s %s %s',
                $name,
                sanitize_title($item),
                $isActive ? 'bs-filter__item--active' : '',
				( $limit == 0 || $index < $limit ) ? 'bs-filter__item--visible' : ''
            );
            ?>
            <li class="<?php echo esc_attr($itemClass); ?>" data-value="<?php echo esc_attr($item); ?>">
                <span><?php echo esc_html($prefix . $item . $postfix); ?></span>
                <input type="checkbox" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr($item); ?>" <?php checked($isActive); ?>>
            </li>
            <?php
        }
        ?>
        </ul>
        <?php if (!$hideSearch && $limit != 0 && count($items) > $limit) : ?>
            <button class="bs-filter__btn-show_more">
                <?php esc_html_e('Show more', 'deepknead'); ?>
            </button>
        <?php endif; ?>
    </div>
</div>
