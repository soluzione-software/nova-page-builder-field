<template>
    <div :id="containerId"></div>
</template>

<script>

    import grapesjs from 'grapesjs';
    import basicBlocks from 'grapesjs-blocks-basic';
    import pluginNavbar from 'grapesjs-navbar';
    import pluginCountdown from 'grapesjs-component-countdown';
    import pluginForms from 'grapesjs-plugin-forms';
    import pluginExport from 'grapesjs-plugin-export';
    // import pluginAviary from 'grapesjs-aviary';
    // import pluginFilestack from 'grapesjs-plugin-filestack';
    import pluginSwiperSlider from 'grapesjs-swiper-slider';
    import pluginCustomCode from 'grapesjs-custom-code';
    import pluginHeader from 'grapesjs-plugin-header';
    import pluginTabs from 'grapesjs-tabs';
    import tUIImageEditor from 'grapesjs-tui-image-editor';
    import custom from '../plugins/custom';
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {

        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data() {
            return {
                editor: null,
                containerId: 'editor-' + Math.random().toString(36).substr(2, 5),
            }
        },

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || '';
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                let js_rx = /<script[^]*<\/script>/gm;
                let empty_js_rx = /<script[^>]*><\/script>/g;

                let html_js = this.editor.getHtml();
                let html = html_js.replace(js_rx, '');
                let css = this.editor.getCss();
                if (css.length > 0){
                    css = '<style>' + css + '</style>';
                }
                let js = '<script>' + this.editor.getJs() + '<\/script>';

                let m;
                do {
                    m = js_rx.exec(html_js);
                    if (m) {
                        js += m[0];
                    }
                } while (m);

                // remove empty script tags
                js = js.replace(empty_js_rx, '');

                formData.append(this.field.attribute, JSON.stringify({html: html, css: css, js: js}));
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value;
                this.editor.setComponents(value);
            },
        },

        mounted() {
            this.editor = grapesjs.init({
                container: '#' + this.containerId,
                allowScripts: 1,
                storageManager: { autoload: 0 },
                width: '100%',
                plugins: [
                    basicBlocks,
                    // pluginAviary,
                    pluginExport,
                    pluginCountdown,
                    // pluginFilestack,
                    pluginForms,
                    pluginNavbar,
                    pluginSwiperSlider,
                    pluginCustomCode,
                    pluginHeader,
                    pluginTabs,
                    tUIImageEditor,
                    custom
                ],
                pluginsOpts: {
                    [basicBlocks]: {
                        flexGrid: true,
                    },
                    [pluginSwiperSlider]: {
                        sliderProps: {
                            scrollbar: false,
                            style: {
                                'height': '100%',
                                'min-height': '50px',
                                'overflow': 'hidden'
                            }
                        },
                        // Default slides
                        slideEls: `
                          <div class="swiper-slide" id="swiper-slide-1"><div style="height: 100%; background-color: rgba(0,0,0,0.1);"><p>Slide 1</p></div></div>
                          <div class="swiper-slide" id="swiper-slide-2"><div style="height: 100%; background-color: rgba(0,0,0,0.1);"><p>Slide 2</p></div></div>
                          <div class="swiper-slide" id="swiper-slide-3"><div style="height: 100%; background-color: rgba(0,0,0,0.1);"><p>Slide 3</p></div></div>
                        `,
                    },
                    [pluginForms]: { /* options */ }
                },
                styleManager : {
                    sectors: [
                        {
                            name: 'General',
                            open: false,
                            buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom']
                        },{
                            name: 'Dimension',
                            open: false,
                            buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                        },{
                            name: 'Typography',
                            open: false,
                            buildProps: ['font-family', 'font-size', 'text-align', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-shadow'],
                        },{
                            name: 'Decorations',
                            open: false,
                            buildProps: ['border-radius-c', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                        },{
                            name: 'Extra',
                            open: false,
                            buildProps: ['transition', 'perspective', 'transform'],
                        }
                    ],
                },
            });

            this.editor.setComponents(this.value);
        }
    }
</script>
