<x-app-layout>
    @php
    $content_types = [
    'notes' => 'Notes',
    'exercise' => 'Exercise'
    ];
    $question_types = [
    'short' => 'Short Question',
    'mc' => 'Multiple Choice'
    ];
    $difficulty_types = [
    'easy' => 'Easy',
    'medium' => 'Medium',
    'advanced' => 'Advanced',
    ];
    $rowLength = json_decode($content->exercise_details) ? count(json_decode($content->exercise_details)) : 1;
    $option_1 = '';
    $option_2 = '';
    $option_3 = '';
    $option_4 = '';
    @endphp
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Create Learning Materials') }}
                </div>
            </div>
            <div class="mt-4">
                <form id="update_materials">
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Title') }}
                        </x-input-label>
                        <x-text-input id="title" class="block mt-2 w-full" type="text" name="title" value="{{ $content->title }}" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Description') }}
                        </x-input-label>
                        <x-text-input id="description" class="block mt-2 w-full" type="text" name="description" value="{{ $content->description }}" required />
                    </div>
                    <div class="mb-4">
                        <x-select id="type" name="type" class="mt-2 hidden" :options="$content_types" :defaultValue="$default_content_type" />
                    </div>
                    <div class="mb-4">
                        <x-input-label class="mb-2">
                            {{ __('Tags') }}
                        </x-input-label>
                        <input
                            id="tags"
                            type="text"
                            class="w-full px-1 py-2 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="tags"
                            value="{{ $default_tags }}" />
                    </div>
                    @if ($content->pdf_url)
                    <x-checkbox id="regenerate_tags" name="regenerate_tags"></x-checkbox>
                    <x-input-label for="regenerate_tags" class="inline-block mb-5 ml-1 text-xs">{{ __('Re-generate Tags with Topic Modeling') }}</x-input-label>
                    @endif
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Points') }}
                        </x-input-label>
                        <x-text-input id="points" class="block mt-2 w-full" type="number" name="points" value="{{ $content->points }}" required />
                    </div>

                    <div class="mb-4 text-editor-section">
                        <x-text-editor id="content" />
                        <input type="hidden" name="pdf_content" id="pdf_content" value="">
                        <div id="error-message" class="text-sm text-red-600 dark:text-red-400 mt-2"></div>
                    </div>
                    <div class="mb-4 table-section hidden">
                        <table id="data-table" class="table-auto border-collapse w-full bg-white dark:bg-gray-900">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        <x-input-label>
                                            {{ __('Question') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        <x-input-label>
                                            {{ __('Type') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        <x-input-label>
                                            {{ __('Answer') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        <x-input-label>
                                            {{ __('Actions') }}
                                        </x-input-label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="main-table">
                                @if ($content->exercise_details)
                                @foreach (json_decode($content->exercise_details) as $i => $q)
                                <tr class="main-row">
                                    <td class="px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        @php
                                        $question_text = $q->question;
                                        @endphp
                                        <x-textarea name="question[{{ $i }}]" :defaultValue="$question_text"></x-textarea>
                                    </td>
                                    <td class="mc-table px-4 py-3 border border-gray-300 dark:border-gray-700">
                                        <table class="table-auto w-full bg-white dark:bg-gray-900">
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>A</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    @php
                                                    $option_1 = html_entity_decode($q->mc[0]);
                                                    @endphp
                                                    <x-textarea name="mc[{{ $i }}][0]" :defaultValue="$option_1"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="answer_[{{ $i }}]" value="A" {{ $q->answer == 'A' ? "checked" : ''}} class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>B</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    @php
                                                    $option_2 = html_entity_decode($q->mc[1]);
                                                    @endphp
                                                    <x-textarea name="mc[{{ $i }}][1]" :defaultValue="$option_2"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="answer_[{{ $i }}]" value="B" {{ $q->answer == 'B' ? "checked" : ''}} class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>C</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    @php
                                                    $option_3 = html_entity_decode($q->mc[2]);
                                                    @endphp
                                                    <x-textarea name="mc[{{ $i }}][2]" :defaultValue="$option_3"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="answer_[{{ $i }}]" value="C" {{ $q->answer == 'C' ? "checked" : ''}} class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>D</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    @php
                                                    $option_4 = html_entity_decode($q->mc[3]);
                                                    @endphp
                                                    <x-textarea name="mc[{{ $i }}][3]" :defaultValue="$option_4"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="answer_[{{ $i }}]" value="D" {{ $q->answer == 'D' ? "checked" : ''}} class="ml-2">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-300 dark:border-gray-700">
                                        <table class="table-auto w-full bg-white dark:bg-gray-900">
                                            <tr>
                                                <td>
                                                    <x-success-button class="add-row">
                                                        <i class="material-icons">&#xe145;</i>
                                                    </x-success-button>
                                                </td>
                                                @if ($i > 0)
                                                <td>
                                                    <x-danger-button class="remove-row">
                                                        <i class="material-icons">&#xe872;</i>
                                                    </x-danger-button>
                                                </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    #loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
    }

    #loader {
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -20px;
        margin-top: -20px;
        width: 40px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 5px solid #000000;
        animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
    }

    @keyframes l20-1 {
        0% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%)
        }

        12.5% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 0%, 100% 0%, 100% 0%)
        }

        25% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 100% 100%, 100% 100%)
        }

        50% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%)
        }

        62.5% {
            clip-path: polygon(50% 50%, 100% 0, 100% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%)
        }

        75% {
            clip-path: polygon(50% 50%, 100% 100%, 100% 100%, 100% 100%, 100% 100%, 50% 100%, 0% 100%)
        }

        100% {
            clip-path: polygon(50% 50%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 0% 100%)
        }
    }

    @keyframes l20-2 {
        0% {
            transform: scaleY(1) rotate(0deg)
        }

        49.99% {
            transform: scaleY(1) rotate(135deg)
        }

        50% {
            transform: scaleY(-1) rotate(0deg)
        }

        100% {
            transform: scaleY(-1) rotate(-135deg)
        }
    }
</style>


<script type="module">
    import {
        Editor
    } from 'https://esm.sh/@tiptap/core';
    import StarterKit from 'https://esm.sh/@tiptap/starter-kit';
    import Highlight from 'https://esm.sh/@tiptap/extension-highlight';
    import Underline from 'https://esm.sh/@tiptap/extension-underline';
    import Link from 'https://esm.sh/@tiptap/extension-link';
    import TextAlign from 'https://esm.sh/@tiptap/extension-text-align';
    import HorizontalRule from 'https://esm.sh/@tiptap/extension-horizontal-rule';
    import Image from 'https://esm.sh/@tiptap/extension-image';
    import TextStyle from 'https://esm.sh/@tiptap/extension-text-style';
    import FontFamily from 'https://esm.sh/@tiptap/extension-font-family';
    import {
        Color
    } from 'https://esm.sh/@tiptap/extension-color';
    import Bold from 'https://esm.sh/@tiptap/extension-bold';
    window.addEventListener('load', function() {
        if (document.getElementById("content")) {

            const FontSizeTextStyle = TextStyle.extend({
                addAttributes() {
                    return {
                        fontSize: {
                            default: null,
                            parseHTML: element => element.style.fontSize,
                            renderHTML: attributes => {
                                if (!attributes.fontSize) {
                                    return {};
                                }
                                return {
                                    style: 'font-size: ' + attributes.fontSize
                                };
                            },
                        },
                    };
                },
            });
            const CustomBold = Bold.extend({
                // Override the renderHTML method
                renderHTML({
                    mark,
                    HTMLAttributes
                }) {
                    const {
                        style,
                        ...rest
                    } = HTMLAttributes;

                    // Merge existing styles with font-weight
                    const newStyle = 'font-weight: bold;' + (style ? ' ' + style : '');

                    return ['span', {
                        ...rest,
                        style: newStyle.trim()
                    }, 0];
                },
                // Ensure it doesn't exclude other marks
                addOptions() {
                    return {
                        ...this.parent?.(),
                        HTMLAttributes: {},
                    };
                },
            });
            // tip tap editor setup
            const editor = new Editor({
                element: document.querySelector('#content'),
                extensions: [
                    // Exclude the default Bold mark
                    StarterKit.configure({
                        marks: {
                            bold: false,
                        },
                    }),
                    // Include the custom Bold extension
                    CustomBold,
                    TextStyle,
                    Color,
                    FontSizeTextStyle,
                    FontFamily,
                    Highlight,
                    Underline,
                    Link.configure({
                        openOnClick: false,
                        autolink: true,
                        defaultProtocol: 'https',
                    }),
                    TextAlign.configure({
                        types: ['heading', 'paragraph'],
                    }),
                    HorizontalRule,
                    Image,
                ],
                content: `{!! $pdf_content !!}`,
                editorProps: {
                    attributes: {
                        class: 'format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none',
                    },
                }
            });


            // set up custom event listeners for the buttons
            document.getElementById('toggleBoldButton').addEventListener('click', () => editor.chain().focus().toggleBold().run());
            document.getElementById('toggleItalicButton').addEventListener('click', () => editor.chain().focus().toggleItalic().run());
            document.getElementById('toggleUnderlineButton').addEventListener('click', () => editor.chain().focus().toggleUnderline().run());
            document.getElementById('toggleStrikeButton').addEventListener('click', () => editor.chain().focus().toggleStrike().run());
            document.getElementById('toggleHighlightButton').addEventListener('click', () => {
                const isHighlighted = editor.isActive('highlight');
                // when using toggleHighlight(), judge if is is already highlighted.
                editor.chain().focus().toggleHighlight({
                    color: isHighlighted ? undefined : '#ffc078' // if is already highlighted，unset the highlight color
                }).run();
            });

            document.getElementById('toggleLinkButton').addEventListener('click', () => {
                const url = window.prompt('Enter image URL:', 'https://flowbite.com');
                editor.chain().focus().toggleLink({
                    href: url
                }).run();
            });
            document.getElementById('removeLinkButton').addEventListener('click', () => {
                editor.chain().focus().unsetLink().run()
            });
            document.getElementById('toggleCodeButton').addEventListener('click', () => {
                editor.chain().focus().toggleCode().run();
            })

            document.getElementById('toggleLeftAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('left').run();
            });
            document.getElementById('toggleCenterAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('center').run();
            });
            document.getElementById('toggleRightAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('right').run();
            });
            document.getElementById('toggleListButton').addEventListener('click', () => {
                editor.chain().focus().toggleBulletList().run();
            });
            document.getElementById('toggleOrderedListButton').addEventListener('click', () => {
                editor.chain().focus().toggleOrderedList().run();
            });
            document.getElementById('toggleBlockquoteButton').addEventListener('click', () => {
                editor.chain().focus().toggleBlockquote().run();
            });
            document.getElementById('toggleHRButton').addEventListener('click', () => {
                editor.chain().focus().setHorizontalRule().run();
            });
            document.getElementById('addImageButton').addEventListener('click', () => {
                const url = window.prompt('Enter image URL:', 'https://placehold.co/600x400');
                if (url) {
                    editor.chain().focus().setImage({
                        src: url
                    }).run();
                }
            });

            // typography dropdown
            const typographyDropdown = FlowbiteInstances.getInstance('Dropdown', 'typographyDropdown');

            document.getElementById('toggleParagraphButton').addEventListener('click', () => {
                editor.chain().focus().setParagraph().run();
                typographyDropdown.hide();
            });

            document.querySelectorAll('[data-heading-level]').forEach((button) => {
                button.addEventListener('click', () => {
                    const level = button.getAttribute('data-heading-level');
                    editor.chain().focus().toggleHeading({
                        level: parseInt(level)
                    }).run()
                    typographyDropdown.hide();
                });
            });

            const textSizeDropdown = FlowbiteInstances.getInstance('Dropdown', 'textSizeDropdown');

            // Loop through all elements with the data-text-size attribute
            document.querySelectorAll('[data-text-size]').forEach((button) => {
                button.addEventListener('click', () => {
                    const fontSize = button.getAttribute('data-text-size');

                    // Apply the selected font size via pixels using the TipTap editor chain
                    editor.chain().focus().setMark('textStyle', {
                        fontSize
                    }).run();

                    // Hide the dropdown after selection
                    textSizeDropdown.hide();
                });
            });

            // Listen for color picker changes
            const colorPicker = document.getElementById('color');
            colorPicker.addEventListener('input', (event) => {
                const selectedColor = event.target.value;

                // Apply the selected color to the selected text
                editor.chain().focus().setColor(selectedColor).run();
            })

            document.querySelectorAll('[data-hex-color]').forEach((button) => {
                button.addEventListener('click', () => {
                    const selectedColor = button.getAttribute('data-hex-color');

                    // Apply the selected color to the selected text
                    editor.chain().focus().setColor(selectedColor).run();
                });
            });

            document.getElementById('reset-color').addEventListener('click', () => {
                editor.commands.unsetColor();
            })

            const fontFamilyDropdown = FlowbiteInstances.getInstance('Dropdown', 'fontFamilyDropdown');

            // Loop through all elements with the data-font-family attribute
            document.querySelectorAll('[data-font-family]').forEach((button) => {
                button.addEventListener('click', () => {
                    const fontFamily = button.getAttribute('data-font-family');

                    // Apply the selected font size via pixels using the TipTap editor chain
                    editor.chain().focus().setFontFamily(fontFamily).run();

                    // Hide the dropdown after selection
                    fontFamilyDropdown.hide();
                });
            });


            $('#update_materials').submit(function(e) {
                e.preventDefault();

                $('#loader-overlay').show();

                var input = $("#pdf_content");
                input.attr('value', editor.getHTML());
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('content.update', $content) }}",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#loader-overlay').hide();

                        window.location.href = "{{ route('content') }}";
                    },
                    error: function(xhr) {
                        $('#loader-overlay').hide();

                        var errors = xhr.responseJSON;
                        if (errors && errors.error) {
                            // Display the error message on the page
                            $('#error-message').text(errors.error);
                        }
                    }
                });
            });

            if ($('html').hasClass('dark')) {
                $('#loader').css('border-color', '#FFFFFF');
            } else {
                $('#loader').css('border-color', '#000000');
            }

            var rowIndex = "{{ $rowLength }}";

            function addNewRow() {
                var newRow = '<tr class="main-row">' +
                    '<td class="px-4 py-2 border border-gray-300 dark:border-gray-700">' +
                    `<x-textarea name="question[` + rowIndex + `]"></x-textarea>` +
                    '</td>' +
                    '<td class="mc-table px-4 py-2 border border-gray-300 dark:border-gray-700">' +
                    '<table class="table-auto w-full bg-white dark:bg-gray-900">' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>A</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    `<x-textarea name="mc[` + rowIndex + `][0]"></x-textarea>` +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="answer_[' + rowIndex + ']" value="A" checked class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>B</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    `<x-textarea name="mc[` + rowIndex + `][1]"></x-textarea>` +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="answer_[' + rowIndex + ']" value="B" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>C</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    `<x-textarea name="mc[` + rowIndex + `][2]"></x-textarea>` +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="answer_[' + rowIndex + ']" value="C" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>D</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    `<x-textarea name="mc[` + rowIndex + `][3]"></x-textarea>` +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="answer_[' + rowIndex + ']" value="D" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</td>' +
                    '<td class="px-4 py-2 border border-gray-300 dark:border-gray-700">' +
                    '<table class="table-auto w-full bg-white dark:bg-gray-900">' +
                    '<tr>' +
                    '<td>' +
                    `<x-success-button class="add-row"><i class="material-icons">&#xe145;</i></x-success-button>` +
                    '</td>' +
                    '<td>' +
                    `<x-danger-button class="remove-row"><i class="material-icons">&#xe872;</i></x-danger-button>` +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</td>' +
                    '</tr>';

                $('#data-table .main-table').append(newRow);
                rowIndex++; // Increment row index for the next row
            }

            $(document).on('click', '.add-row', function(event) {
                event.preventDefault(); // Prevent default click event behavior
                $(this).off('click'); // Unbind the click event
                addNewRow(); // Add a new row
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('.main-row').remove();
            });

            function checkContentType() {
                var selectedType = $('#type').val();
                if (selectedType === 'notes') {
                    $('.text-editor-section').show();
                    $('.table-section').hide();
                } else {
                    $('.text-editor-section').hide();
                    $('.table-section').show();
                }
            }

            checkContentType();

            // Listen for change in the "type" select input
            $('#type').on('change', function() {
                checkContentType();
            });

            function checkQuestionType() {
                var selectedType = $(this).val();

                // Find the closest parent <tr> element
                var parentRow = $(this).closest('tr');

                // Find the nearest .short-answer and .mc-table elements within the same row
                var shortAnswer = parentRow.find('.short-answer');
                var mcTable = parentRow.find('.mc-table');

                if (selectedType === 'short') {
                    shortAnswer.show();
                    mcTable.hide();
                } else {
                    shortAnswer.hide();
                    mcTable.show();
                }
            }

            $('.question-type').each(function() {
                checkQuestionType.call(this);
            });

            $(document).on('change', '.question-type', function() {
                checkQuestionType();
            });

            var input = document.querySelector('#tags');
            var tags = JSON.parse('{!! addslashes($tags) !!}');
            var tagify = new Tagify(input, {
                addTagOnBlur: false,
                editTags: false,
                dropdown: {
                    enabled: 0,
                    closeOnSelect: false,
                },
                whitelist: tags,
                enforceWhitelist: true,
            });

            tagify.on('add', () => function onAddTag(e) {
                tagify.off('add', onAddTag) // exmaple of removing a custom Tagify event
            });
        }
    })
</script>