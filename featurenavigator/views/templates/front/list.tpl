{*
* Copyright 2024 Stefan Schulz
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.md.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please email schulz@the-loom.de,
* so we can send you a copy immediately.
*
* @author    Stefan Schulz <schulz@the-loom.de>
* @copyright 2024 Stefan Schulz
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{extends file='layouts/layout-left-column.tpl'}

{block name="left_column"}
    <div id="left-column" class="col-xs-12 col-sm-4 col-md-3 feature-navigator">
        <div class="nav-bar card card-block">
            {foreach range('A', 'Z') as $char}
                <div><a href="/{$baseUrl}/list/{$char}" class="nav-link">{$char}</a></div>
            {/foreach}
            <div><a href="/{$baseUrl}/list/_" class="nav-link">#</a></div>
        </div>
    </div>
{/block}

{block name="content_wrapper"}
    <div id="content-wrapper" class="js-content-wrapper left-column col-xs-12 col-sm-8 col-md-9 feature-navigator">
        {block name="heading"}
            <p>Some heading.</p>
        {/block}
        {block name="content"}
            <p>Hello world! This is HTML5 Boilerplate.</p>
        {/block}
    </div>
{/block}

{block name="heading"}
    <div class="block-category card card-block">
        <h1 class="h1">{$heading}</h1>
        <div class="block-category-inner">
            <div id="category-description" class="text-muted">
                <p>{l s='Listing for %letter%' sprintf=['%letter%' => strtoupper($letter)] d='Modules.Featurenavigator.Front'}</p>
            </div>
        </div>
    </div>
{/block}

{block name='content'}
    <section id="main">

        <section id="items">
            <div class="item-list">
                <!-- source: {$source}; order: {$direction}; letter: {$letter} -->
                {if empty($entries)}
                    {if $letter == '#'}
                        <p class="no-articles">{l s='No articles found starting with non-alphabetic letters.' d='Modules.Featurenavigator.Front'}</p>
                    {else}
                        <p class="no-articles">{l s='No articles found at %letter%.' sprintf=['%letter%' => strtoupper($letter)] d='Modules.Featurenavigator.Front'}</p>
                    {/if}
                {else}
                    <ul>
                        {foreach $entries as $entry}
                            <li><a href="/{$baseUrl}/products/{$entry.param}" class="feature-link">{$entry.topic}</a></li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        </section>

    </section>
{/block}
