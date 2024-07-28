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
    <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
        <div class="nav-bar">
            {foreach range('A', 'Z') as $char}
                <a href="/{$baseUrl}/{$char}" class="nav-link">{$char}</a>
            {/foreach}
            <a href="/{$baseUrl}/_" class="nav-link">#</a>
        </div>
    </div>
{/block}

{block name='content'}
    <section id="main">

        <section id="items">
            <div class="feature-navigator">
                <!-- source: {$source}; order: {$direction}; letter: {$letter} -->
                {if empty($entries)}
                    {if $letter == '#'}
                        <h3>{l s='No articles found starting with non-alphabetic letters.' sprintf=['%letter%' => $letter] d='Modules.Featurenavigator.Front'}</h3>
                    {else}
                        <h3>{l s='No articles found at %letter%.' sprintf=['%letter%' => strtoupper($letter)] d='Modules.Featurenavigator.Front'}</h3>
                    {/if}
                {else}
                    <ul>
                        {foreach $entries as $entry}
                            <li>{$entry.topic}</li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        </section>

    </section>

{/block}
