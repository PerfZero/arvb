(function (blocks, element, components, blockEditor, serverSideRender) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var ServerSideRender = serverSideRender;

    registerBlockType("spbau/toc", {
        title: "Содержание статьи",
        icon: "list-view",
        category: "widgets",
        supports: {
            html: false,
        },
        attributes: {
            title: {
                type: "string",
                default: "Содержание статьи",
            },
            includeH3: {
                type: "boolean",
                default: true,
            },
            targetSelector: {
                type: "string",
                default: ".single-article__content",
            },
        },
        edit: function (props) {
            var attrs = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        {
                            title: "Настройки содержания",
                            initialOpen: true,
                        },
                        el(TextControl, {
                            label: "Заголовок блока",
                            value: attrs.title || "",
                            onChange: function (value) {
                                setAttributes({ title: value });
                            },
                        }),
                        el(ToggleControl, {
                            label: "Добавлять заголовки H3",
                            checked: !!attrs.includeH3,
                            onChange: function (value) {
                                setAttributes({ includeH3: !!value });
                            },
                        }),
                        el(TextControl, {
                            label: "CSS селектор контейнера статьи",
                            help: "По умолчанию: .single-article__content",
                            value: attrs.targetSelector || "",
                            onChange: function (value) {
                                setAttributes({ targetSelector: value });
                            },
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: "spbau/toc",
                    attributes: attrs,
                })
            );
        },
        save: function () {
            return null;
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor,
    window.wp.serverSideRender
);
