<?php

namespace App\Services;

use SVG\SVG;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Texts\SVGText;
use SVG\Nodes\Texts\SVGTSpan;

class ImageTextService
{
    public function addTextToImage($text, $x = 23, $y = 90, $color = 'black', $size = 23, $font = 'Arial', $maxWidth = 150)
    {
        $svgPath = public_path('assets/img/document3.svg');
        if (!file_exists($svgPath)) {
            throw new \Exception("SVG file not found at: " . $svgPath);
        }
    
        // Load the SVG
        $svg = SVG::fromFile($svgPath);
        $doc = $svg->getDocument();
    
        // Directly target the first <g> element
        $group = $doc->getChild(0);
        if (!$group instanceof SVGGroup) {
            throw new \Exception("The first element is not a <g> tag.");
        }
    
        // Split the stored name into parts
        $names = array_filter(explode('@', $text)); // Remove empty values
        $numLines = count($names);
    
        $lineHeight = $size * 1.2; // Adjust line spacing
        $startY = $y;
    
        // Dynamically adjust start position based on number of lines
        if ($numLines == 1) {
            $startY = $y + ($lineHeight * 1.7); // Shift downward
        } elseif ($numLines == 2) {
            $startY = $y + ($lineHeight * 1.3); // Slight shift
        } elseif ($numLines == 3) {
            $startY = $y + ($lineHeight * 0.65 ); // Shift up for more text
        } elseif ($numLines == 4) {
            $startY = $y + ($lineHeight * 0.15 ); // Shift up for more text
        }
    
        foreach ($names as $index => $name) {
            $textElement = new SVGText();
            $textElement->setAttribute('x', $x);
            $textElement->setAttribute('y', $startY + ($index * $lineHeight)); // Adjust vertical position
            $textElement->setStyle('fill', 'rgb(0, 38, 85)');
            $textElement->setStyle('font-size', "{$size}px");
            $textElement->setStyle('font-family', $font);
            // $textElement->setAttribute('text-anchor', 'middle'); 
    
            // Wrap text if needed
            $words = explode(' ', $name);
            $lines = [];
            $currentLine = '';
    
            foreach ($words as $word) {
                $testLine = trim($currentLine . ' ' . $word);
                $testWidth = strlen($testLine) * ($size / 2);
    
                if ($testWidth > $maxWidth) {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                } else {
                    $currentLine = $testLine;
                }
            }
            if (!empty($currentLine)) {
                $lines[] = $currentLine;
            }
    
            $currentY = $startY + ($index * $lineHeight);
    
            foreach ($lines as $line) {
                $tspan = new SVGTSpan();
                $tspan->setValue($line);
                $tspan->setAttribute('x', $x);
                $tspan->setAttribute('dy', $lineHeight);
    
                $textElement->addChild($tspan);
            }
    
            $group->addChild($textElement);
        }
    
        // Save the modified SVG
        $folderPath = 'assets/img/contracts';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    
        $outputFileName = 'output-' . time() . '-' . rand(1000, 9999) . '.svg';
        $outputPath = $folderPath . '/' . $outputFileName;
    
        file_put_contents($outputPath, $svg->toXMLString());
    
        return $outputPath;
    }

}

?>
