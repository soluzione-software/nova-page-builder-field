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

            loadAssets() {
                Nova.request().get(this.getUrl)
                    .then((response) => {
                        this.editor.AssetManager.add(response.data);
                    });
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
                assetManager: {
                    // Default assets
                    // eg. [
                    //  'https://...image1.png',
                    //  'https://...image2.png',
                    //  {type: 'image', src: 'https://...image3.png', someOtherCustomProp: 1},
                    //  ..
                    // ]
                    assets: [],

                    // Content to add where there is no assets to show
                    // eg. 'No <b>assets</b> here, drag to upload'
                    noAssets: '',

                    // Upload endpoint, set `false` to disable upload
                    // upload: 'https://endpoint/upload/assets',
                    // upload: false,
                    upload: this.field.storeAssets ? this.postUrl : false,

                    // The name used in POST to pass uploaded files
                    uploadName: 'file',

                    // Custom headers to pass with the upload request
                    headers: {},

                    // Custom parameters to pass with the upload request, eg. csrf token
                    params: {
                        '_token': this.field.csrf_token,
                    },

                    // The credentials setting for the upload request, eg. 'include', 'omit'
                    credentials: 'include',

                    // Allow uploading multiple files per request.
                    // If disabled filename will not have '[]' appended
                    multiUpload: false,

                    // If true, tries to add automatically uploaded assets.
                    // To make it work the server should respond with a JSON containing assets
                    // in a data key, eg:
                    // {
                    //  data: [
                    //    'https://.../image.png',
                    //    ...
                    //    {src: 'https://.../image2.png'},
                    //    ...
                    //  ]
                    // }
                    autoAdd: 1,

                    // Text on upload input
                    uploadText: 'Drop files here or click to upload',

                    // Label for the add button
                    addBtnText: 'Add image',

                    // Custom uploadFile function
                    // @example
                    // uploadFile: (e) => {
                    //   var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
                    //   // ...send somewhere
                    // }
                    uploadFile: '',

                    // Handle the image url submit from the built-in 'Add image' form
                    // @example
                    // handleAdd: (textFromInput) => {
                      // some check...
                    //   editor.AssetManager.add(textFromInput);
                    // }
                    handleAdd: '',

                    // Enable an upload dropzone on the entire editor (not document) when dragging
                    // files over it
                    // dropzone: 1, NOTE: breaks everything

                    // Open the asset manager once files are been dropped via the dropzone
                    openAssetsOnDrop: 1,

                    // Any dropzone content to append inside dropzone element
                    dropzoneContent: '',

                    // Default title for the asset manager modal
                    modalTitle: 'Select Image',
                }
            });

            this.editor.setComponents(this.value);

            this.loadAssets();
        },

        computed: {
            getUrl() {
                return `/nova-vendor/page-builder-field/${this.resourceName}/${this.field.attribute}`;
            },
            postUrl() {
                return `/nova-vendor/page-builder-field/${this.resourceName}/${this.field.attribute}`;
            },
        }
    }
</script>
