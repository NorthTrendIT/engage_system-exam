<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changelog</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .toc {
            position: fixed;
            top: 10px;
            left: 20px;
            width: 200px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .toc h2 {
            font-size: 18px;
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .toc ul {
            list-style-type: none;
            padding-left: 0;
        }

        .toc ul li {
            margin: 10px 0;
        }

        .toc a {
            text-decoration: none;
            color: #007bff;
        }

        .changelog {
            margin-left: 250px;
            padding: 20px;
        }

        details {
            margin: 10px 0;
        }

        details summary {
            font-weight: bold;
            cursor: pointer;
        }

        details p {
            margin: 5px 0;
        }

        #scrollToTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }

        #scrollToTopBtn:hover {
            background-color: #0056b3;
        }

        /* Search Bar Styling */
        .search-container {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #searchQuery {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #clearSearch {
            background: none;
            border: none;
            font-size: 18px;
            color: #007bff;
            cursor: pointer;
            padding: 0;
            margin-left: -30px;
        }

        .highlight {
            background-color: yellow;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            color: red;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <!-- Table of Contents -->
    <div class="toc">
        <h2>Table of Contents</h2>
        <!-- Search Bar Below Table of Contents -->
        <div class="search-container">
            <input type="text" id="searchQuery" placeholder="Search changelog..." onkeyup="debouncedSearch()">
            <button id="clearSearch" onclick="clearSearch()" style="display: none;">&times;</button>
        </div>
        <ul id="tocList">
            @foreach ($toc as $section)
                <li><a
                        href="#{{ strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $section))) }}">{{ $section }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Changelog Content -->
    <div class="changelog" id="changelogContent">
        {!! $htmlContent !!}
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" onclick="scrollToTop()">↑</button>

    <script>
        let debounceTimeout;

        // Debounced search function
        function debouncedSearch() {
            const query = document.getElementById('searchQuery').value;

            // Clear previous debounce timeout
            clearTimeout(debounceTimeout);

            // Set new timeout to delay search execution
            debounceTimeout = setTimeout(function() {
                searchChangelog(query);
            }, 500); // 500ms delay after the user stops typing
        }

        // Search Functionality
        function searchChangelog(query) {
            // Show or hide the 'X' button based on input
            var clearButton = document.getElementById('clearSearch');
            if (query === '') {
                clearButton.style.display = 'none';
                // If query is empty, restore original content
                restoreOriginalContent();
                return;
            } else {
                clearButton.style.display = 'inline-block';
            }

            fetch('{{ url()->current() }}?query=' + query, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.filteredContent.trim() === '') {
                        document.getElementById('changelogContent').innerHTML =
                            '<p class="no-results">No results found.</p>';
                    } else {
                        document.getElementById('changelogContent').innerHTML = data.filteredContent;
                    }

                    // Update the Table of Contents (TOC)
                    if (data.toc && data.toc.length > 0) {
                        document.getElementById('tocList').innerHTML = data.toc.map(function(section) {
                            return '<li><a href="#' + section.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '">' +
                                section + '</a></li>';
                        }).join('');
                    } else {
                        document.getElementById('tocList').innerHTML = '<li>No matching sections.</li>';
                    }

                    // Re-initialize smooth scroll behavior for new TOC links
                    addSmoothScroll();
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                });
        }

        // Clear search input
        function clearSearch() {
            document.getElementById('searchQuery').value = '';
            debouncedSearch();
        }

        // Restore original content when query is empty
        function restoreOriginalContent() {
            // Restore original changelog content (this assumes the full changelog is available on initial page load)
            document.getElementById('changelogContent').innerHTML = `{!! $htmlContent !!}`;

            // Restore original Table of Contents (TOC)
            document.getElementById('tocList').innerHTML =
                @foreach ($toc as $section)
                    '<li><a href="#{{ strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $section))) }}">{{ $section }}</a></li>' +
                @endforeach
            '';

            // Re-initialize smooth scroll for the restored TOC
            addSmoothScroll();
        }

        // Smooth scroll behavior for TOC links
        function addSmoothScroll() {
            const tocLinks = document.querySelectorAll('.toc a');
            tocLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const targetId = link.getAttribute('href').substring(1); // Remove the # symbol
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100, // Adjust this offset as needed
                            behavior: 'smooth'
                        });
                    }
                });
            });
        }

        // Scroll to Top Button
        window.onscroll = function() {
            var scrollButton = document.getElementById("scrollToTopBtn");
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                scrollButton.style.display = "block";
            } else {
                scrollButton.style.display = "none";
            }
        };

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Initialize smooth scroll on page load
        document.addEventListener('DOMContentLoaded', function() {
            addSmoothScroll();
        });
    </script>
</body>

</html>
