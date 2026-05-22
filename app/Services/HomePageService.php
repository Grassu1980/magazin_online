<?php

namespace App\Services;

use App\Models\HomepageSection;

class HomePageService
{
    /**
     * Obține secțiunile active ale homepage-ului ordonate
     */
    public function getActiveSections()
    {
        return HomepageSection::active()
            ->ordered()
            ->get();
    }

    /**
     * Obține datele pentru homepage
     */
    public function getHomePageData()
    {
        $sections = $this->getActiveSections();
        
        $data = [];
        
        foreach ($sections as $section) {
            $sectionData = [
                'id' => $section->id,
                'type' => $section->type,
                'title' => $section->title,
                'config' => $section->config,
            ];

            // Adaugă date specifice în funcție de tipul secțiunii
            switch ($section->type) {
                case 'slider':
                case 'banners_row':
                    $sectionData['banners'] = $section->getBanners();
                    break;
                    
                case 'products_grid':
                    $sectionData['products'] = $section->getProducts();
                    break;
                    
                case 'categories_grid':
                    $sectionData['categories'] = $section->getCategories();
                    break;
                    
                case 'custom_html':
                    $sectionData['html_content'] = $section->config['html_content'] ?? '';
                    break;
                    
                case 'video':
                    $sectionData['video_url'] = $section->config['video_url'] ?? '';
                    $sectionData['video_title'] = $section->config['video_title'] ?? '';
                    $sectionData['video_description'] = $section->config['video_description'] ?? '';
                    break;
                    
                case 'text_block':
                    $sectionData['text_content'] = $section->config['text_content'] ?? '';
                    $sectionData['background_color'] = $section->config['background_color'] ?? '#ffffff';
                    $sectionData['text_color'] = $section->config['text_color'] ?? '#000000';
                    break;
            }

            $data[] = $sectionData;
        }

        return $data;
    }

    /**
     * Obține bannerele pentru o anumită poziție
     */
    public function getBannersByPosition($position)
    {
        return \App\Models\Banner::active()
            ->byPosition($position)
            ->ordered()
            ->get();
    }

    /**
     * Obține bannerele pentru slider
     */
    public function getSliderBanners()
    {
        return $this->getBannersByPosition('slider');
    }

    /**
     * Obține bannerele pentru poziția top
     */
    public function getTopBanners()
    {
        return $this->getBannersByPosition('top');
    }

    /**
     * Obține bannerele pentru poziția middle
     */
    public function getMiddleBanners()
    {
        return $this->getBannersByPosition('middle');
    }

    /**
     * Obține bannerele pentru poziția bottom
     */
    public function getBottomBanners()
    {
        return $this->getBannersByPosition('bottom');
    }

    /**
     * Obține bannerele pentru poziția footer
     */
    public function getFooterBanners()
    {
        return $this->getBannersByPosition('footer');
    }
}
