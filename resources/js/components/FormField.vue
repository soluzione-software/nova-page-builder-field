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
                    custom
                ],
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
                            buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-shadow'],
                        },{
                            name: 'Decorations',
                            open: false,
                            buildProps: ['border-radius-c', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                        }
                    ],
                },
            });

            this.editor.setComponents(this.value);
        }
    }
</script>
