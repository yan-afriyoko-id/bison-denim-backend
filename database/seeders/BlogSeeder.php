<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\CategoryBlog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BlogSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            'style-guide' => CategoryBlog::where('slug', 'style-guide')->value('id'),
            'trend-fashion' => CategoryBlog::where('slug', 'trend-fashion')->value('id'),
            'tips-perawatan' => CategoryBlog::where('slug', 'tips-perawatan')->value('id'),
            'koleksi-terbaru' => CategoryBlog::where('slug', 'koleksi-terbaru')->value('id'),
        ];

        $blogs = [
            [
                'title' => '5 Ways to Style Your Jeans for a Casual Chic Look',
                'slug' => '5-ways-to-style-your-jeans-for-a-casual-chic-look',
                'fk_category' => $categories['style-guide'],
                'short_desc' => 'Get inspired with these mix & match jean ideas for everyday looks that remain elegant and classy.',
                'long_desc' => '<p>Jeans are one of the most versatile fashion items that everyone should have in their wardrobe. With the right styling, jeans can give you a casual yet elegant look. Here are 5 ways you can try:</p>
<h2>1. Jeans + White Shirt + Blazer</h2>
<p>This classic combination never fails. Wear slim fit jeans with a plain white shirt and a navy blazer. Add loafers or derby shoes to complete the look.</p>
<h2>2. Jeans + T-Shirt + Denim Jacket</h2>
<p>Double denim or Canadian tuxedo can look stylish if done right. Pair your jeans with a white t-shirt and a denim jacket in a contrasting color.</p>
<h2>3. Jeans + Sweater + Sneakers</h2>
<p>For a comfortable casual look, pair your jeans with a knitted sweater and white sneakers. Perfect for weekend hangouts.</p>
<h2>4. Jeans + Silk Top + Heels</h2>
<p>For women, pair your jeans with a silk blouse and high heels. This combination gives a casual yet luxurious and elegant impression.</p>
<h2>5. Jeans + Turtleneck + Long Coat</h2>
<p>A rainy season look that stays stylish. Pair your jeans with a black turtleneck and long coat. Add boots for a more masculine edge.</p>
<p>With these five ways, your everyday casual look will stay elegant and fashionable. Happy styling!</p>',
                'image_url' => 'https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg',
                'meta_title' => '5 Ways to Style Your Jeans for a Casual Chic Look',
                'meta_description' => 'Get inspired with these mix & match jean ideas for everyday elegant looks.',
                'hot_news' => true,
                'status' => true,
            ],
            [
                'title' => 'Denim Trends 2025: Colors and Cuts You Need to Own',
                'slug' => 'denim-trends-2025-colors-and-cuts-you-need-to-own',
                'fk_category' => $categories['trend-fashion'],
                'short_desc' => 'Stay ahead with the latest denim trends of 2025 from colors, cuts, to popular styles.',
                'long_desc' => '<p>The denim industry keeps evolving every year. Entering 2025, several denim trends are predicted to dominate the fashion scene. Here\'s what you need to own!</p>
<h2>1. Wide Leg Jeans Are Back</h2>
<p>After years of slim and skinny fits dominating, wide leg jeans are making a strong comeback. The loose and comfortable fit gives a relaxed yet stylish look.</p>
<h2>2. Earth Tone Colors</h2>
<p>Beyond classic indigo blue, earth tone colors like olive, caramel, and khaki denim are becoming major trends this year. These colors are easy to pair with any outfit.</p>
<h2>3. Distressed and Raw Hem</h2>
<p>Distressed details and raw hems remain trendy. They add an edgy and casual feel to your jeans.</p>
<h2>4. Denim Patchwork</h2>
<p>The patchwork technique of combining various denim colors and textures creates a unique and artistic look.</p>
<h2>5. High-Waisted Jeans</h2>
<p>High-waisted jeans remain a favorite because they create a leg-lengthening illusion and a neater appearance.</p>
<p>Bison Denim always brings the latest denim collections that follow current fashion trends. Visit our store and find your dream denim!</p>',
                'image_url' => 'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg',
                'meta_title' => 'Denim Trends 2025: Colors and Cuts You Need to Own',
                'meta_description' => 'Stay ahead with the latest denim trends of 2025 from colors, cuts, to popular styles.',
                'hot_news' => true,
                'status' => true,
            ],
            [
                'title' => 'How to Choose the Perfect Denim Jacket for Your Body Type',
                'slug' => 'how-to-choose-the-perfect-denim-jacket-for-your-body-type',
                'fk_category' => $categories['style-guide'],
                'short_desc' => 'A denim jacket is a long-term fashion investment. Pick one that suits your body shape best.',
                'long_desc' => '<p>A denim jacket is a timeless fashion item that can be worn for years. However, choosing the right denim jacket for your body type is crucial for a flattering look. Here\'s your guide:</p>
<h2>For Tall and Slim Bodies</h2>
<p>You\'re lucky! Almost all denim jacket models suit you. Try an oversized denim jacket for a relaxed look or a cropped denim jacket for a more modern style.</p>
<h2>For Athletic/Broad Shoulder Bodies</h2>
<p>Choose a regular fit denim jacket that isn\'t too tight. Avoid styles that are too narrow in the shoulders. Opt for slightly stretchy fabric for maximum comfort.</p>
<h2>For Petite/Short Bodies</h2>
<p>Pick a cropped denim jacket that ends at your waist. This creates a leg-lengthening illusion. Avoid jackets that are too long as they can make you appear shorter.</p>
<h2>For Fuller Bodies</h2>
<p>Choose a relaxed fit denim jacket in darker colors like black or dark indigo. Dark colors create a slimming effect. Avoid overly busy details on the front.</p>
<p>Remember, comfort is key. Make sure your denim jacket gives you enough room to move and doesn\'t feel restrictive.</p>',
                'image_url' => 'https://images.pexels.com/photos/325876/pexels-photo-325876.jpeg',
                'meta_title' => 'How to Choose the Perfect Denim Jacket for Your Body Type',
                'meta_description' => 'A guide to picking the right denim jacket for your body shape for a flattering look.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'How to Care for Your Denim Jeans to Make Them Last Longer',
                'slug' => 'how-to-care-for-your-denim-jeans-to-make-them-last-longer',
                'fk_category' => $categories['tips-perawatan'],
                'short_desc' => 'Essential tips to keep your favorite denim jeans looking sharp and lasting for years.',
                'long_desc' => '<p>Denim is a fashion investment that can last for years if cared for properly. Unfortunately, many people make mistakes when caring for their denim jeans. Here are the complete tips:</p>
<h2>1. Don\'t Wash Too Often</h2>
<p>Denim jeans don\'t need to be washed after every wear. Wash them after 4-5 wears or only when they are truly dirty. Washing too frequently will cause the color to fade quickly.</p>
<h2>2. Turn Jeans Inside Out Before Washing</h2>
<p>Turn your jeans inside out before washing. This protects the outer color and prevents excessive fading.</p>
<h2>3. Use Cold Water</h2>
<p>Wash denim jeans with cold water, not warm or hot water. Hot water can damage denim fibers and cause rapid color fading.</p>
<h2>4. Avoid the Dryer</h2>
<p>Never put denim jeans in a tumble dryer. The heat can damage denim fibers and cause shrinkage. Hang dry in a shaded area.</p>
<h2>5. Store Properly</h2>
<p>Hang your denim jeans or fold them neatly. Avoid storing in damp places as it can cause mold and unpleasant odors.</p>
<p>With proper care, your favorite denim jeans can last for years and still look like new.</p>',
                'image_url' => 'https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg',
                'meta_title' => 'How to Care for Your Denim Jeans to Make Them Last Longer',
                'meta_description' => 'Essential tips to keep your denim jeans looking sharp and lasting for years.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'New Arrival: Bison Denim Summer 2025 Collection',
                'slug' => 'new-arrival-bison-denim-summer-2025-collection',
                'fk_category' => $categories['koleksi-terbaru'],
                'short_desc' => 'Bison Denim launches its latest summer denim collection with lighter and more breathable fabrics.',
                'long_desc' => '<p>Summer has arrived and Bison Denim is proud to present our latest denim collection designed specifically for hot weather. With lighter and more breathable fabrics, you can stay stylish without feeling stuffy.</p>
<h2>Lightweight 7oz Denim</h2>
<p>Our latest collection uses 7oz denim fabric, significantly lighter than standard denim (12-14oz). Perfect for Indonesia\'s tropical climate.</p>
<h2>Brighter Colors</h2>
<p>Beyond classic denim shades, we bring brighter colors like pastel blue, light grey, and beige that suit the summer vibe.</p>
<h2>Looser Fits</h2>
<p>Following global trends, our summer collection features straight leg and wide leg cuts that provide better air circulation.</p>
<h2>Woven Details and Embroidery</h2>
<p>Traditional Indonesian woven details and embroidery add aesthetic value to every product. Making your denim not only comfortable but also unique.</p>
<p>This collection is now available at all Bison Denim stores and our official website. Get yours before they run out!</p>',
                'image_url' => 'https://images.pexels.com/photos/1183266/pexels-photo-1183266.jpeg',
                'meta_title' => 'New Arrival: Bison Denim Summer 2025 Collection',
                'meta_description' => 'Bison Denim launches its latest summer denim collection with lightweight and breathable fabrics.',
                'hot_news' => true,
                'status' => true,
            ],
            [
                'title' => 'Can You Wear Denim to Semi-Formal Events?',
                'slug' => 'can-you-wear-denim-to-semi-formal-events',
                'fk_category' => $categories['style-guide'],
                'short_desc' => 'Who says denim can\'t be worn to semi-formal events? Find out how here.',
                'long_desc' => '<p>Denim is often considered too casual for semi-formal events. But with the right styling, denim can be a great choice for occasions like dinner dates, anniversary parties, or less formal office events.</p>
<h2>Choose Dark Denim</h2>
<p>For semi-formal events, opt for dark-colored denim like black denim or dark indigo. Dark colors give a more formal and elegant impression compared to light blue denim.</p>
<h2>Avoid Rips and Distressing</h2>
<p>Jeans with rips, patches, or excessive distressing look too casual. Choose plain, clean denim without too many details.</p>
<h2>Pair with Formal Tops</h2>
<p>Combine denim with a formal shirt, blazer, or silk top. Add formal shoes like loafers, oxfords, or heels to complete the look.</p>
<h2>The Right Accessories</h2>
<p>Use accessories like a classic watch, leather belt, or structured bag to elevate the formality of your denim outfit.</p>
<p>In conclusion, yes you can wear denim to semi-formal events. The key is in choosing the right color, material, and styling!</p>',
                'image_url' => 'https://images.pexels.com/photos/238385/pexels-photo-238385.jpeg',
                'meta_title' => 'Can You Wear Denim to Semi-Formal Events?',
                'meta_description' => 'Tips on styling denim for semi-formal events to keep it elegant and classy.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'Selvedge vs Non-Selvedge Denim: What You Need to Know',
                'slug' => 'selvedge-vs-non-selvedge-denim-what-you-need-to-know',
                'fk_category' => $categories['trend-fashion'],
                'short_desc' => 'What is selvedge denim? Learn the differences before buying your next pair of jeans.',
                'long_desc' => '<p>For true denim enthusiasts, the term selvedge is already familiar. But for beginners, the difference between selvedge and non-selvedge denim might still be confusing. Let\'s break it down!</p>
<h2>What Is Selvedge Denim?</h2>
<p>Selvedge (self-edge) is a type of denim woven on traditional shuttle looms. The result is a clean fabric edge that won\'t unravel easily. Selvedge is associated with high quality and traditional manufacturing.</p>
<h2>Key Differences</h2>
<p><strong>Manufacturing Process:</strong> Selvedge is made on slower shuttle looms that produce tighter and stronger fabric. Non-selvedge uses modern projectile looms that are faster.</p>
<p><strong>Price:</strong> Selvedge denim is typically more expensive due to the more complex production process and longer manufacturing time.</p>
<p><strong>Fade Pattern:</strong> Selvedge denim produces more unique and personal fade patterns over time (raw denim).</p>
<p><strong>Signature Detail:</strong> Selvedge has a red line (or other colors) along the fabric edge, visible when the jeans are cuffed.</p>
<h2>Which One Should You Choose?</h2>
<p>If you value the highest quality and appreciate traditional craftsmanship, selvedge is the right choice. But if you\'re looking for affordable denim with good quality, non-selvedge is equally great.</p>',
                'image_url' => 'https://images.pexels.com/photos/5710078/pexels-photo-5710078.jpeg',
                'meta_title' => 'Selvedge vs Non-Selvedge Denim: What You Need to Know',
                'meta_description' => 'Learn the differences between selvedge and non-selvedge denim before buying your next jeans.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'Tips for Storing Jeans to Prevent Odor and Mold',
                'slug' => 'tips-for-storing-jeans-to-prevent-odor-and-mold',
                'fk_category' => $categories['tips-perawatan'],
                'short_desc' => 'How to store your jeans properly to avoid musty smells and mold in humid climates.',
                'long_desc' => '<p>Indonesia\'s high humidity makes storing clothes, especially denim, a challenge. Here are tips for keeping your jeans fresh and mold-free.</p>
<h2>1. Make Sure They\'re Completely Dry</h2>
<p>Before storing, ensure your jeans are completely dry. Residual moisture is the main cause of mold and musty smells. Dry thoroughly, especially pockets and waistbands.</p>
<h2>2. Use Silica Gel or Mothballs</h2>
<p>Place silica gel or mothballs in your storage closet. These absorb excess moisture and prevent mold growth.</p>
<h2>3. Don\'t Overstack</h2>
<p>Avoid stacking too many jeans in one place. Poor air circulation between piles can trap moisture and trigger mold.</p>
<h2>4. Air Them Out Periodically</h2>
<p>If you have jeans you rarely wear, take them out and air them in a shaded area every few weeks to prevent moisture buildup.</p>
<h2>5. Use the Right Hangers</h2>
<p>For long-term storage, use wide hangers or fold your jeans neatly. Avoid thin wire hangers that can leave marks on the fabric.</p>
<p>With proper storage, your denim jeans will stay fresh and last longer even when not worn frequently.</p>',
                'image_url' => 'https://images.pexels.com/photos/4577030/pexels-photo-4577030.jpeg',
                'meta_title' => 'Tips for Storing Jeans to Prevent Odor and Mold',
                'meta_description' => 'How to store jeans properly in tropical climates to avoid musty smells and mold.',
                'hot_news' => false,
                'status' => true,
            ],
            // --- 8 additional blogs ---
            [
                'title' => 'The History of Denim: From Workwear to Fashion Icon',
                'slug' => 'the-history-of-denim-from-workwear-to-fashion-icon',
                'fk_category' => $categories['trend-fashion'],
                'short_desc' => 'Explore the fascinating journey of denim from humble workwear to a global fashion staple.',
                'long_desc' => '<p>Denim has come a long way since its invention in the 19th century. What started as durable workwear for miners and laborers has transformed into a universal fashion item worn by people of all ages and backgrounds.</p>
<h2>The Birth of Denim</h2>
<p>In 1873, Jacob Davis and Levi Strauss patented the first riveted denim jeans. Originally designed for miners during the California Gold Rush, these pants were valued for their durability and strength.</p>
<h2>Denim in Pop Culture</h2>
<p>The 1950s saw denim become a symbol of rebellion thanks to icons like James Dean and Marlon Brando. In the 1960s and 70s, denim was embraced by the counterculture movement and began to be seen as a fashion statement rather than just workwear.</p>
<h2>Modern Denim</h2>
<p>Today, denim comes in countless cuts, washes, and styles. From high-end designer jeans to affordable basics, denim remains one of the most versatile fabrics in the fashion world. Bison Denim continues this legacy by crafting premium denim that honors tradition while embracing modern style.</p>',
                'image_url' => 'https://images.pexels.com/photos/428340/pexels-photo-428340.jpeg',
                'meta_title' => 'The History of Denim: From Workwear to Fashion Icon',
                'meta_description' => 'Explore the fascinating journey of denim from humble workwear to a global fashion staple.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'Sustainable Denim: How Bison Denim is Going Green',
                'slug' => 'sustainable-denim-how-bison-denim-is-going-green',
                'fk_category' => $categories['trend-fashion'],
                'short_desc' => 'Discover how Bison Denim is embracing eco-friendly practices to create sustainable denim fashion.',
                'long_desc' => '<p>Sustainability is no longer just a buzzword in the fashion industry. At Bison Denim, we are committed to reducing our environmental footprint while delivering the quality denim our customers love.</p>
<h2>Eco-Friendly Materials</h2>
<p>We source organic cotton and recycled denim fibers for our collections. These materials require less water and fewer chemicals during production, making them a greener choice.</p>
<h2>Water Conservation</h2>
<p>Traditional denim production uses massive amounts of water. Our modern washing techniques reduce water consumption by up to 60% compared to conventional methods. We also treat and recycle wastewater to minimize pollution.</p>
<h2>Sustainable Packaging</h2>
<p>All Bison Denim products are packaged using recycled and biodegradable materials. We are continuously looking for ways to reduce plastic waste in our supply chain.</p>
<p>By choosing Bison Denim, you\'re not just investing in great style you\'re also supporting a more sustainable future for fashion.</p>',
                'image_url' => 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg',
                'meta_title' => 'Sustainable Denim: How Bison Denim is Going Green',
                'meta_description' => 'Discover how Bison Denim is embracing eco-friendly practices to create sustainable denim fashion.',
                'hot_news' => true,
                'status' => true,
            ],
            [
                'title' => 'Denim for Every Season: Styling Tips for Rainy Weather',
                'slug' => 'denim-for-every-season-styling-tips-for-rainy-weather',
                'fk_category' => $categories['style-guide'],
                'short_desc' => 'Stay stylish even on rainy days with these denim outfit ideas that work in wet weather.',
                'long_desc' => '<p>Rainy weather doesn\'t mean you have to sacrifice style. With the right denim pieces and styling, you can stay fashionable even when the skies are gray.</p>
<h2>Dark Denim is Your Friend</h2>
<p>Dark wash denim is perfect for rainy days. It hides water spots better than lighter washes and looks more polished for indoor events after the rain passes.</p>
<h2>Pair with Boots</h2>
<p>Combat boots or Chelsea boots pair perfectly with denim on rainy days. Not only do they keep your feet dry, but they also add an edgy touch to your outfit.</p>
<h2>Layer with a Denim Jacket</h2>
<p>A classic denim jacket is the perfect layering piece for rainy weather. It adds warmth without being too heavy, and it\'s durable enough to handle light moisture.</p>
<h2>Roll Up Your Hem</h2>
<p>Rolling up your jeans hem keeps the fabric from dragging on wet ground. It also shows off your boots and adds a casual, carefree vibe to your look.</p>
<p>Don\'t let a little rain ruin your style. With these tips, you can rock your denim all year round!</p>',
                'image_url' => 'https://images.pexels.com/photos/1536619/pexels-photo-1536619.jpeg',
                'meta_title' => 'Denim for Every Season: Styling Tips for Rainy Weather',
                'meta_description' => 'Stay stylish even on rainy days with these denim outfit ideas that work in wet weather.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'The Perfect Denim Outfit for a Job Interview',
                'slug' => 'the-perfect-denim-outfit-for-a-job-interview',
                'fk_category' => $categories['style-guide'],
                'short_desc' => 'Yes, you can wear denim to a job interview. Here\'s how to make it professional and polished.',
                'long_desc' => '<p>Job interviews require a polished appearance, but that doesn\'t always mean a full suit. With the right denim ensemble, you can look professional while expressing your personal style.</p>
<h2>Choose the Right Wash</h2>
<p>For interviews, always go with dark or black denim. Light wash or distressed jeans are too casual for professional settings. Dark denim creates a sharp, clean silhouette that commands respect.</p>
<h2>Pair with a Blazer</h2>
<p>A well-fitted blazer instantly elevates any denim outfit. Choose a navy, charcoal, or black blazer and pair it with a crisp button-down shirt or a simple blouse.</p>
<h2>Footwear Matters</h2>
<p>Choose closed-toe shoes like loafers, oxfords, or low heels. Avoid sneakers, flip-flops, or anything too casual. Your shoes should complement the professional vibe of your outfit.</p>
<h2>Keep Accessories Minimal</h2>
<p>A simple watch, a leather belt, and perhaps a subtle piece of jewelry are all you need. Less is more when it comes to interview attire.</p>
<p>With these tips, you can confidently wear denim to your next job interview and make a lasting impression.</p>',
                'image_url' => 'https://images.pexels.com/photos/1154861/pexels-photo-1154861.jpeg',
                'meta_title' => 'The Perfect Denim Outfit for a Job Interview',
                'meta_description' => 'Yes, you can wear denim to a job interview. Here\'s how to make it professional and polished.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'DIY Denim: How to Customize Your Jeans at Home',
                'slug' => 'diy-denim-how-to-customize-your-jeans-at-home',
                'fk_category' => $categories['tips-perawatan'],
                'short_desc' => 'Give your old jeans a new life with these fun and easy DIY customization ideas.',
                'long_desc' => '<p>Got a pair of old jeans that need a refresh? Before you toss them out, try these DIY customization techniques to give them a brand-new look.</p>
<h2>Distressing and Ripping</h2>
<p>Use sandpaper, a razor blade, or tweezers to create natural-looking fades and rips. Start small and work your way up you can always add more distress but you can\'t take it back!</p>
<h2>Patchwork and Embroidery</h2>
<p>Add fabric patches or embroider your own designs to create a unique, personalized look. Floral patterns, geometric shapes, or your favorite quotes can transform plain jeans into wearable art.</p>
<h2>Bleach Techniques</h2>
<p>Create custom faded patterns using diluted bleach. Use spray bottles, stencils, or tie-dye techniques to achieve one-of-a-kind designs. Always work in a well-ventilated area and protect your surfaces.</p>
<h2>Add Studs and Hardware</h2>
<p>Sew or clamp on studs, rhinestones, or spikes along the pockets, seams, or waistband for a edgy, rock-and-roll vibe. This is an easy way to add instant personality to any pair of jeans.</p>
<p>With a little creativity and some basic supplies, you can turn your old jeans into a fashion statement that\'s uniquely yours.</p>',
                'image_url' => 'https://images.pexels.com/photos/1468379/pexels-photo-1468379.jpeg',
                'meta_title' => 'DIY Denim: How to Customize Your Jeans at Home',
                'meta_description' => 'Give your old jeans a new life with these fun DIY customization ideas.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'Denim Care 101: Removing Stains Without Ruining Your Jeans',
                'slug' => 'denim-care-101-removing-stains-without-ruining-your-jeans',
                'fk_category' => $categories['tips-perawatan'],
                'short_desc' => 'Learn the right way to remove stains from denim without damaging the fabric or color.',
                'long_desc' => '<p>Spilled something on your favorite jeans? Don\'t panic. With the right techniques, you can remove most stains from denim without causing damage.</p>
<h2>Act Quickly</h2>
<p>The sooner you treat a stain, the easier it is to remove. Blot (don\'t rub) the stain with a clean cloth to absorb as much liquid as possible before it sets.</p>
<h2>Use Cold Water</h2>
<p>Always use cold water when treating denim stains. Hot water can set protein-based stains like blood or sweat, making them permanent.</p>
<h2>Mild Detergent is Best</h2>
<p>Avoid harsh chemicals or bleach. Use a mild detergent mixed with cold water and gently dab the stain with a soft brush or cloth. For oil-based stains, a small amount of dish soap can work wonders.</p>
<h2>Air Dry Only</h2>
<p>After treating the stain, let your jeans air dry completely before checking the result. Heat from a dryer can set any remaining stain permanently.</p>
<p>With patience and the right approach, your denim can survive spills and stains, looking great for years to come.</p>',
                'image_url' => 'https://images.pexels.com/photos/1813947/pexels-photo-1813947.jpeg',
                'meta_title' => 'Denim Care 101: Removing Stains Without Ruining Your Jeans',
                'meta_description' => 'Learn the right way to remove stains from denim without damaging the fabric or color.',
                'hot_news' => false,
                'status' => true,
            ],
            [
                'title' => 'Introducing Bison Denim Premium Selvedge Collection',
                'slug' => 'introducing-bison-denim-premium-selvedge-collection',
                'fk_category' => $categories['koleksi-terbaru'],
                'short_desc' => 'Bison Denim launches its premium selvedge denim collection crafted with traditional shuttle looms.',
                'long_desc' => '<p>We are thrilled to announce the launch of our newest premium line: the Bison Denim Selvedge Collection. Crafted with care using traditional shuttle looms, each piece tells a story of quality and heritage.</p>
<h2>What Makes Selvedge Special</h2>
<p>Our selvedge denim is woven on vintage shuttle looms that create a tighter, denser fabric. This results in jeans that not only last longer but also develop beautiful, personalized fade patterns over time.</p>
<h2>Japanese-Inspired Craftsmanship</h2>
<p>We source our selvedge denim from renowned Japanese mills known for their meticulous attention to detail. The result is a fabric with exceptional texture, depth, and character.</p>
<h2>Limited Edition Drops</h2>
<p>Each selvedge collection is produced in limited quantities to ensure the highest quality. When you buy a pair of Bison Denim selvedge jeans, you\'re owning a piece of craftsmanship that few others will have.</p>
<p>Visit our stores or browse online to explore the Bison Denim Premium Selvedge Collection. Experience denim the way it was meant to be made.</p>',
                'image_url' => 'https://images.pexels.com/photos/2897883/pexels-photo-2897883.jpeg',
                'meta_title' => 'Introducing Bison Denim Premium Selvedge Collection',
                'meta_description' => 'Bison Denim launches its premium selvedge denim collection crafted with traditional shuttle looms.',
                'hot_news' => true,
                'status' => true,
            ],
            [
                'title' => 'Denim Gifts: What to Buy for the Denim Lover in Your Life',
                'slug' => 'denim-gifts-what-to-buy-for-the-denim-lover-in-your-life',
                'fk_category' => $categories['koleksi-terbaru'],
                'short_desc' => 'Stuck on gift ideas? Here are the best denim-related gifts for fashion lovers.',
                'long_desc' => '<p>Looking for the perfect gift for someone who loves denim? Whether it\'s for a birthday, holiday, or just because, here are our top denim gift recommendations.</p>
<h2>Premium Denim Jeans</h2>
<p>You can never go wrong with a high-quality pair of jeans. Choose a classic cut like straight leg or slim fit in a versatile dark wash that will complement any wardrobe.</p>
<h2>Denim Jacket</h2>
<p>A denim jacket is a timeless investment piece. Our collection features both classic and modern cuts, perfect for layering in any season.</p>
<h2>Denim Accessories</h2>
<p>From denim caps and bags to belts and wallets, denim accessories add a casual cool factor to any outfit without committing to a full denim look.</p>
<h2>Bison Denim Gift Card</h2>
<p>Not sure about their size or style preference? A Bison Denim gift card lets them choose exactly what they want. Available in-store and online.</p>
<p>Make their day with a gift that combines style, quality, and the timeless appeal of denim.</p>',
                'image_url' => 'https://images.pexels.com/photos/3712095/pexels-photo-3712095.jpeg',
                'meta_title' => 'Denim Gifts: What to Buy for the Denim Lover in Your Life',
                'meta_description' => 'Stuck on gift ideas? Here are the best denim-related gifts for fashion lovers.',
                'hot_news' => false,
                'status' => true,
            ],
        ];

        $disk = Storage::disk('public');

        foreach ($blogs as $blog) {
            $filename = $blog['slug'] . '.jpg';
            $coverPath = 'blogs/' . $filename;

            if (!$disk->exists($coverPath)) {
                $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'Mozilla/5.0']]);
                $imageData = @file_get_contents($blog['image_url'] . '?auto=compress&cs=tinysrgb&w=800&h=500&fit=crop', false, $ctx);

                if ($imageData === false) {
                    $img = imagecreatetruecolor(800, 500);
                    $bg = imagecolorallocate($img, mt_rand(20, 60), mt_rand(40, 80), mt_rand(100, 160));
                    imagefill($img, 0, 0, $bg);
                    $white = imagecolorallocate($img, 255, 255, 255);
                    $text = $blog['title'];
                    $fontSize = 5;
                    $tw = imagefontwidth($fontSize) * strlen($text);
                    $th = imagefontheight($fontSize);
                    $x = (imagesx($img) - $tw) / 2;
                    $y = (imagesy($img) - $th) / 2;
                    imagestring($img, $fontSize, (int)$x, (int)$y, $text, $white);
                    ob_start();
                    imagejpeg($img, null, 80);
                    $imageData = ob_get_clean();
                    imagedestroy($img);
                }

                $disk->put($coverPath, $imageData);
            }

            Blog::updateOrCreate(
                ['slug' => $blog['slug']],
                [
                    'cover' => $coverPath,
                    'title' => $blog['title'],
                    'short_desc' => $blog['short_desc'],
                    'long_desc' => $blog['long_desc'],
                    'fk_category' => $blog['fk_category'],
                    'slug' => $blog['slug'],
                    'meta_title' => $blog['meta_title'],
                    'meta_description' => $blog['meta_description'],
                    'hot_news' => $blog['hot_news'],
                    'status' => $blog['status'],
                ]
            );
        }
    }
}
