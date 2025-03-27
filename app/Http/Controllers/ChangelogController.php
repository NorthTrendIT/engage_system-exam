<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Parsedown;

class ChangelogController extends Controller
{
    public function index(Request $request)
    {
        $filePath = base_path('CHANGELOG.md');

        if (file_exists($filePath)) {
            // Read the contents of the changelog file
            $changelogContent = file_get_contents($filePath);

            // Use Parsedown to convert the Markdown to HTML
            $parsedown = new Parsedown();
            $htmlContent = $parsedown->text($changelogContent);

            // Extract headings to generate Table of Contents (TOC)
            preg_match_all('/<h2.*?>(.*?)<\/h2>/', $htmlContent, $matches);
            $toc = $matches[1]; // Get the section titles

            // Add IDs to the headings to make them linkable
            $htmlContent = preg_replace_callback('/<h2>(.*?)<\/h2>/', function ($matches) {
                $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $matches[1])));
                return '<h2 id="' . $slug . '">' . $matches[1] . '</h2>';
            }, $htmlContent);

            // Add collapsible "details" for long sections
            $htmlContent = preg_replace_callback('/(<h2.*?>.*?<\/h2>)(.*?)(?=<h2|$)/s', function ($matches) {
                $sectionTitle = $matches[1];
                $sectionContent = $matches[2];

                // If the content exceeds 300 characters, make it collapsible
                if (strlen(strip_tags($sectionContent)) > 300) {
                    $summary = '<summary>Click to view more</summary>';
                    return $sectionTitle . '<details>' . $summary . $sectionContent . '</details>';
                }

                return $sectionTitle . $sectionContent;
            }, $htmlContent);

            // Check if the request is an AJAX request (via query)
            $query = $request->input('query', '');

            if ($query) {
                // Perform search if query exists
                $htmlContent = $this->searchContent($htmlContent, $query);
                $toc = $this->filterToc($toc, $query);

                // Return JSON response for AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    // Ensure toc is returned as an array
                    return response()->json([
                        'filteredContent' => $htmlContent ?: '<p>No results found.</p>',
                        'toc' => array_values($toc) ?: ['No matching sections.']
                    ]);
                }
            }

            // Return normal view if not an AJAX request
            return view('changelog.index', compact('htmlContent', 'toc'));
        }

        // If changelog file is not found
        return response()->json(['error' => 'Changelog file not found'], 404);
    }

    private function searchContent($htmlContent, $query)
    {
        // Highlight or filter content based on the query
        $pattern = "/($query)/i";
        $replacement = '<span class="highlight">$1</span>';
        return preg_replace($pattern, $replacement, $htmlContent);
    }

    // private function searchContent($htmlContent, $query)
    // {
    //     // Escape the query to avoid errors in regex
    //     $escapedQuery = preg_quote($query, '/');

    //     // Match the text inside the HTML content, avoiding HTML tags
    //     $htmlContent = preg_replace_callback('/>([^<]+)</', function ($matches) use ($escapedQuery) {
    //         $content = $matches[1];
    //         // Only highlight the query if it's found inside the content
    //         if (stripos($content, $escapedQuery) !== false) {
    //             $content = preg_replace('/(' . $escapedQuery . ')/i', '<span class="highlight">$1</span>', $content);
    //         }
    //         return '>' . $content . '<';
    //     }, $htmlContent);

    //     return $htmlContent;
    // }

    private function filterToc($toc, $query)
    {
        // Filter the table of contents based on the query
        return array_filter($toc, function ($section) use ($query) {
            return stripos($section, $query) !== false;
        });
    }
}
