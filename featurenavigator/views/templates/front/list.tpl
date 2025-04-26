{*
 * Copyright 2025 Stefan Schulz
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Stefan Schulz <schulz@the-loom.de>
 * @copyright 2025 Stefan Schulz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
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
            {include file="module:featurenavigator/views/templates/front/_partials/features_top.tpl" heading=$heading letter=$letter}
        {/block}
        {block name="content"}
            <p>Hello world! This is HTML5 Boilerplate.</p>
        {/block}
    </div>
{/block}

{block name='content'}
    <section id="main">

        <section id="items">
            <div class="item-list">
                {if empty($entries)}
                    {if $letter == '#'}
                        <p class="no-articles">{l s='No articles found starting with non-alphabetic letters.' d='Modules.Featurenavigator.Front'}</p>
                    {else}
                        <p class="no-articles">{l s='No articles found at %letter%.' sprintf=['%letter%' => strtoupper($letter)] d='Modules.Featurenavigator.Front'}</p>
                    {/if}
                {else}
                    {include file="module:featurenavigator/views/templates/front/_partials/features.tpl" entries=$entries baseUrl=$baseUrl}
                {/if}
            </div>
        </section>

    </section>
{/block}
