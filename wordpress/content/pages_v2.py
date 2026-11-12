"""Illustrated layouts for text-only pages (EN / AR).
Placeholders resolved on the server: {{img:ID}} -> image URL, {{link:key}} -> page URL."""
import json, html as H

E = lambda s: s  # content is trusted, authored here


def intro(eye, lead, paras, img, alt, flip=False):
    ps = ''.join(f'<p>{p}</p>' for p in paras)
    cls = 'ni-wa-intro ni-wa-intro--flip' if flip else 'ni-wa-intro'
    return (f'<div class="{cls}"><div><div class="ni-eyebrow"><span></span>{eye}</div>'
            f'<p class="ni-wa-intro__lead">{lead}</p>{ps}</div>'
            f'<div class="ni-wa-intro__photo"><img src="{{{{img:{img}}}}}" alt="{alt}" loading="lazy"></div></div>')


def cards(eye, h2, items, cols=3, numbered=False, intro_p=''):
    out = []
    for n, it in enumerate(items, 1):
        icon, title, text = it[0], it[1], it[2]
        sub = it[3] if len(it) > 3 else ''
        badge = f'<b class="ni-feat__num">{n:02d}</b>' if numbered else f'<i class="ph {icon}" aria-hidden="true"></i>'
        sub_h = f'<small>{sub}</small>' if sub else ''
        out.append(f'<div class="ni-feat__card">{badge}<h3>{title}</h3>{sub_h}<p>{text}</p></div>')
    ip = f'<p class="ni-sec__p">{intro_p}</p>' if intro_p else ''
    return (f'<div class="ni-sec"><div class="ni-eyebrow"><span></span>{eye}</div><h2 class="ni-wa-h2">{h2}</h2>{ip}'
            f'<div class="ni-feat ni-feat--{cols}">{"".join(out)}</div></div>')


def split(img, alt, eye, h2, items):
    rows = ''.join(f'<div class="ni-wa-partner"><i class="ph {i}" aria-hidden="true"></i><div><h3>{t}</h3><p>{p}</p></div></div>' for i, t, p in items)
    return (f'<div class="ni-wa-model ni-sec"><div class="ni-wa-photo"><img src="{{{{img:{img}}}}}" alt="{alt}" loading="lazy"></div>'
            f'<div><div class="ni-eyebrow"><span></span>{eye}</div><h2 class="ni-wa-h2">{h2}</h2><div class="ni-wa-partners">{rows}</div></div></div>')


def band(quote, points):
    pts = ''.join(f'<div class="ni-wa-outcome"><b>{n:02d}</b><div><h4>{t}</h4><p>{p}</p></div></div>' for n, (t, p) in enumerate(points, 1))
    return f'<div class="ni-wa-band"><blockquote>{quote}</blockquote><div class="ni-wa-outcomes">{pts}</div></div>'


def strip(imgs):
    return '<div class="ni-strip">' + ''.join(f'<figure><img src="{{{{img:{i}}}}}" alt="{a}" loading="lazy"><figcaption>{a}</figcaption></figure>' for i, a in imgs) + '</div>'


def tcards(eye, h2, items):
    out = ''.join(f'<div class="ni-tcard"><div class="ni-tcard__img"><img src="{{{{img:{img}}}}}" alt="" loading="lazy"><b>{n:02d}</b></div>'
                  f'<div class="ni-tcard__body"><h3>{t}</h3><p>{p}</p></div></div>' for n, (img, t, p) in enumerate(items, 1))
    return f'<div class="ni-sec"><div class="ni-eyebrow"><span></span>{eye}</div><h2 class="ni-wa-h2">{h2}</h2><div class="ni-tgrid">{out}</div></div>'


def statements(items):
    out = ''.join(f'<div class="ni-statement ni-statement--{c}"><i class="ph {i}" aria-hidden="true"></i><div class="ni-eyebrow"><span></span>{e}</div><p>{t}</p></div>' for c, i, e, t in items)
    return f'<div class="ni-statements ni-sec">{out}</div>'


def sdgs(eye, h2, items):
    out = ''.join(f'<div class="ni-sdg" style="--sdg:{col}"><b>{num}</b><h3>{t}</h3><p>{p}</p></div>' for num, col, t, p in items)
    return f'<div class="ni-sec"><div class="ni-eyebrow"><span></span>{eye}</div><h2 class="ni-wa-h2">{h2}</h2><div class="ni-sdgs">{out}</div></div>'


def cta(title, text, label, key):
    return (f'<div class="ni-cta ni-sec"><div><h2>{title}</h2><p>{text}</p></div>'
            f'<a class="ni-cta__btn" href="{{{{link:{key}}}}}">{label}</a></div>')


def wrap(*blocks):
    return '<div class="ni-wa">' + ''.join(blocks) + '</div>'


PAGES = {}

def page(key, en, ar):
    PAGES[key] = {'en': en, 'ar': ar}

# ---------------------------------------------------------------- About
page('whoWeAre',
 wrap(intro('Who we are', 'An independent institute connecting policy, evidence and action.', [
   'The NIKON Institute is a Somalia-based research, learning and development organisation bridging the gap between policy, evidence and action to advance the Sustainable Development Goals across Somalia and the Horn of Africa.',
   'We work with development innovators, researchers, practitioners, policy-makers, service users and donors, strengthening human capacity through training, research and knowledge sharing.'], 4048, 'NIKON Institute at a regional cooperation conference'),
  cards('What we do', 'Four ways we turn evidence into change.', [
   ('ph-magnifying-glass', 'Research', 'Rigorous, participatory research that answers real development questions.'),
   ('ph-chalkboard-teacher', 'Training & learning', 'Courses, workshops and fellowships that strengthen people and institutions.'),
   ('ph-bank', 'Policy engagement', 'Dialogue with decision-makers so that evidence shapes policy.'),
   ('ph-handshake', 'Partnership', 'Co-creation among academia, the development sector and communities.')], cols=4),
  band('We anchor our model in the <em>tripartite approach</em>: academia, the development sector and local communities working side by side.', [
   ('Equitable research', 'We promote fair, inclusive research and partnerships across Somalia and the Horn of Africa.'),
   ('Aligned with the SDGs', 'Our evidence informs humanitarian and development programming across the 17 goals.'),
   ('Rooted locally', 'Communities are partners in shaping, delivering and sustaining solutions.')]),
  strip([(4405, 'International Women’s Day 2026'), (3887, 'Community dialogue'), (4250, 'Partners at work')]),
  cta('Get to know us better', 'Read our vision and mission, the values that guide us and the way we work.', 'Vision & mission →', 'vision')),
 wrap(intro('من نحن', 'معهد مستقل يربط السياسات بالأدلة والعمل.', [
   'معهد نيكون منظمة صومالية للبحث والتعلّم والتنمية، تعمل على سدّ الفجوة بين السياسات والأدلة والعمل للنهوض بأهداف التنمية المستدامة في الصومال والقرن الأفريقي.',
   'نعمل مع المبتكرين في مجال التنمية والباحثين والممارسين وصنّاع السياسات والمستفيدين من الخدمات والمانحين، ونعزّز القدرات البشرية عبر التدريب والبحث وتبادل المعرفة.'], 4048, 'معهد نيكون في مؤتمر للتعاون الإقليمي'),
  cards('ما نقوم به', 'أربع طرق نحوّل بها الأدلة إلى تغيير.', [
   ('ph-magnifying-glass', 'البحث', 'بحوث دقيقة وتشاركية تجيب عن أسئلة التنمية الفعلية.'),
   ('ph-chalkboard-teacher', 'التدريب والتعلّم', 'دورات وورش عمل وزمالات تقوّي الأفراد والمؤسسات.'),
   ('ph-bank', 'التأثير في السياسات', 'حوار مع صنّاع القرار لتصوغ الأدلة السياسات.'),
   ('ph-handshake', 'الشراكة', 'ابتكار مشترك بين الأكاديميا وقطاع التنمية والمجتمعات.')], cols=4),
  band('نرسّخ نموذجنا على <em>النهج الثلاثي</em>: الأكاديميا وقطاع التنمية والمجتمعات المحلية يعملون جنبًا إلى جنب.', [
   ('بحث منصف', 'نعزّز البحث والشراكات العادلة والشاملة في الصومال والقرن الأفريقي.'),
   ('متوافق مع أهداف التنمية', 'تُثري أدلتنا البرامج الإنسانية والتنموية عبر الأهداف السبعة عشر.'),
   ('متجذّر محليًا', 'المجتمعات شريكة في تصميم الحلول وتنفيذها واستدامتها.')]),
  strip([(4405, 'اليوم العالمي للمرأة 2026'), (3887, 'حوار مجتمعي'), (4250, 'شركاء في العمل')]),
  cta('تعرّف علينا أكثر', 'اقرأ رؤيتنا ورسالتنا والقيم التي توجّهنا وطريقة عملنا.', 'الرؤية والرسالة ←', 'vision')))

page('vision',
 wrap(intro('Vision & mission', 'Inclusive, timely and reliable data driving sustainable development across the Horn of Africa.', [
   'Everything we do starts from a simple conviction: better decisions come from better evidence, and the best evidence is built together with the people it concerns.'], 3904, 'Community meeting under the trees'),
  statements([('navy', 'ph-eye', 'Our vision', 'We envision a Horn of Africa where inclusive, timely and reliable data drives sustainable development and empowers informed decision-making at all levels.'),
              ('gold', 'ph-target', 'Our mission', 'To generate and use quality data and research to inform policy, strengthen practice and advance sustainable development across Somalia and the Horn of Africa.')]),
  cards('How we pursue it', 'From data to decisions.', [
   ('ph-database', 'Quality data', 'We collect and analyse data that is inclusive, timely and reliable.'),
   ('ph-lightbulb', 'Useful research', 'We turn data into findings that answer the questions decision-makers face.'),
   ('ph-arrows-clockwise', 'Lasting practice', 'We help institutions and communities put evidence to work, and learn as they go.')]),
  strip([(3883, 'Listening to communities'), (4063, 'Policy dialogue'), (1669, 'Investing in learning')])),
 wrap(intro('الرؤية والرسالة', 'بيانات شاملة وموثوقة وفي حينها تقود التنمية المستدامة في القرن الأفريقي.', [
   'ينطلق كل ما نقوم به من قناعة بسيطة: القرارات الأفضل تأتي من أدلة أفضل، وأفضل الأدلة هي التي تُبنى بالشراكة مع من تعنيهم.'], 3904, 'اجتماع مجتمعي تحت الأشجار'),
  statements([('navy', 'ph-eye', 'رؤيتنا', 'نتطلع إلى قرن أفريقي تقود فيه البيانات الشاملة والموثوقة والمتاحة في حينها التنمية المستدامة، وتمكّن من اتخاذ قرارات مستنيرة على جميع المستويات.'),
              ('gold', 'ph-target', 'رسالتنا', 'توليد البيانات والبحوث عالية الجودة واستخدامها لإثراء السياسات وتعزيز الممارسات والنهوض بالتنمية المستدامة في الصومال والقرن الأفريقي.')]),
  cards('كيف نحقق ذلك', 'من البيانات إلى القرارات.', [
   ('ph-database', 'بيانات عالية الجودة', 'نجمع بيانات شاملة وموثوقة وفي حينها ونحلّلها.'),
   ('ph-lightbulb', 'بحوث مفيدة', 'نحوّل البيانات إلى نتائج تجيب عن أسئلة صنّاع القرار.'),
   ('ph-arrows-clockwise', 'ممارسة مستدامة', 'نساعد المؤسسات والمجتمعات على توظيف الأدلة والتعلّم المستمر.')]),
  strip([(3883, 'الإصغاء إلى المجتمعات'), (4063, 'حوار السياسات'), (1669, 'الاستثمار في التعليم')])))

VAL_EN = [('ph-shield-check', 'Accountability', 'We conduct research with transparency and responsibility, prioritising trust, ethical standards and meaningful engagement with communities.'),
 ('ph-users-three', 'Inclusion', 'Our research is participatory and respectful, ensuring that marginalised and local communities are actively involved and their knowledge is valued.'),
 ('ph-handshake', 'Collaboration', 'We work in partnership with researchers, communities and stakeholders, fostering shared learning and co-creation.'),
 ('ph-scales', 'Integrity', 'We work with honesty and respect, safeguarding the dignity, rights and knowledge of the communities we engage.'),
 ('ph-star', 'Excellence', 'We pursue quality and innovation so our research delivers meaningful, practical and sustainable impact.')]
VAL_AR = [('ph-shield-check', 'المساءلة', 'نجري بحوثنا بشفافية ومسؤولية، ونولي الأولوية للثقة والمعايير الأخلاقية والتواصل الهادف مع المجتمعات.'),
 ('ph-users-three', 'الإدماج', 'بحوثنا تشاركية تحترم الجميع، وتضمن مشاركة المجتمعات المحلية والمهمّشة وتقدير معارفها.'),
 ('ph-handshake', 'التعاون', 'نعمل بالشراكة مع الباحثين والمجتمعات والأطراف المعنية، ونعزّز التعلّم والابتكار المشترك.'),
 ('ph-scales', 'النزاهة', 'نعمل بأمانة واحترام، ونصون كرامة المجتمعات التي نعمل معها وحقوقها ومعارفها.'),
 ('ph-star', 'التميّز', 'نسعى إلى الجودة والابتكار لتحقق بحوثنا أثرًا عمليًا ومستدامًا.')]
page('values',
 wrap(intro('Core values', 'Five values guide how we research, partner and serve communities.', [
   'They shape the questions we ask, the way we collect and share data, and how we stand alongside the communities we work with.'], 3899, 'Women taking part in a community consultation'),
  cards('What we stand for', 'Our values in practice.', VAL_EN, cols=3),
  band('Communities are <em>partners, not subjects</em>, in every piece of research we do.', [
   ('Ethics first', 'Informed consent, privacy and dignity come before any data point.'),
   ('Shared ownership', 'Findings are returned to, and discussed with, the communities that shaped them.')])),
 wrap(intro('قيمنا الأساسية', 'خمس قيم توجّه طريقتنا في البحث والشراكة وخدمة المجتمعات.', [
   'تصوغ هذه القيم الأسئلة التي نطرحها، وطريقة جمع البيانات ومشاركتها، ووقوفنا إلى جانب المجتمعات التي نعمل معها.'], 3899, 'نساء يشاركن في مشاورة مجتمعية'),
  cards('ما نؤمن به', 'قيمنا في الممارسة.', VAL_AR, cols=3),
  band('المجتمعات <em>شريكة لا موضوع للبحث</em> في كل عمل نقوم به.', [
   ('الأخلاقيات أولًا', 'الموافقة المستنيرة والخصوصية والكرامة قبل أي معلومة.'),
   ('ملكية مشتركة', 'نعيد النتائج إلى المجتمعات التي أسهمت فيها ونناقشها معها.')])))

TH_EN = [(4250, 'Research and knowledge exchange', 'We generate, share and apply evidence, integrating scientific research, indigenous knowledge and community experience.'),
 (1803, 'Inclusive governance and leadership', 'We strengthen institutions, leadership and participatory decision-making, so that women, youth and marginalised groups shape priorities.'),
 (3313, 'Climate action and community resilience', 'We support locally led adaptation that strengthens resilience, protects ecosystems and manages natural resources sustainably.'),
 (3300, 'Community empowerment and sustainable livelihoods', 'We co-create practical solutions that improve livelihoods, promote economic opportunity and build long-term wellbeing.'),
 (3889, 'Peacebuilding, dialogue and social cohesion', 'We facilitate dialogue and collaboration that foster trust, prevent conflict and promote social cohesion.')]
TH_AR = [(4250, 'البحث وتبادل المعرفة', 'نولّد الأدلة ونتبادلها ونطبّقها، ونجمع بين البحث العلمي والمعرفة المحلية وتجارب المجتمعات.'),
 (1803, 'الحوكمة الشاملة والقيادة', 'نقوّي المؤسسات والقيادة وصنع القرار التشاركي لتشارك النساء والشباب والفئات المهمّشة في تحديد الأولويات.'),
 (3313, 'العمل المناخي وصمود المجتمعات', 'ندعم التكيّف بقيادة محلية الذي يعزّز الصمود ويحمي النظم البيئية ويدير الموارد الطبيعية باستدامة.'),
 (3300, 'تمكين المجتمعات وسبل العيش المستدامة', 'نبتكر مع المجتمعات حلولًا عملية تحسّن سبل العيش وتوسّع الفرص الاقتصادية وتعزّز الرفاه.'),
 (3889, 'بناء السلام والحوار والتماسك الاجتماعي', 'نيسّر الحوار والتعاون اللذين يبنيان الثقة ويمنعان النزاعات ويعزّزان التماسك الاجتماعي.')]
page('thematic',
 wrap(intro('Our thematic areas', 'Five themes where evidence can make the greatest difference.', [
   'Our research, learning and policy work is organised around the issues that matter most to communities in Somalia and the Horn of Africa.'], 3900, 'Community members in a participatory session'),
  tcards('Where we focus', 'Our five thematic areas.', TH_EN),
  cta('See the themes in action', 'Explore the programmes through which we deliver this work.', 'Our programs →', 'grp_menuPrograms')),
 wrap(intro('مجالاتنا المواضيعية', 'خمسة مجالات يمكن للأدلة أن تُحدث فيها أكبر فرق.', [
   'ننظّم أعمالنا البحثية والتعليمية والسياساتية حول القضايا الأهم للمجتمعات في الصومال والقرن الأفريقي.'], 3900, 'أفراد من المجتمع في جلسة تشاركية'),
  tcards('أين نركّز', 'مجالاتنا المواضيعية الخمسة.', TH_AR),
  cta('شاهد هذه المجالات على أرض الواقع', 'اكتشف البرامج التي ننفّذ من خلالها هذا العمل.', 'برامجنا ←', 'grp_menuPrograms')))

page('sdg',
 wrap(intro('Our commitment to the SDGs', 'The Sustainable Development Goals give our work a shared direction.', [
   'They give us a common language with our partners. We advance the goals by generating evidence, strengthening capacity and convening the people who can turn goals into results on the ground.'], 3826, 'The 17 Sustainable Development Goals'),
  sdgs('Where we contribute most', 'Five goals at the heart of our work.', [
   ('04', '#C5192D', 'Quality education', 'Training and learning that build skills and knowledge.'),
   ('05', '#FF3A21', 'Gender equality', 'Advancing women in leadership and entrepreneurship.'),
   ('08', '#A21942', 'Decent work and economic growth', 'Research and skills that support livelihoods and employment.'),
   ('16', '#00689D', 'Peace, justice and strong institutions', 'Evidence and dialogue that strengthen governance and institutions.'),
   ('17', '#19486A', 'Partnerships for the goals', 'Co-creation among academia, the development sector and local communities.')]),
  strip([(4401, 'Women in leadership'), (3299, 'Learning and skills'), (4064, 'Dialogue and institutions')])),
 wrap(intro('التزامنا بأهداف التنمية المستدامة', 'تمنح أهداف التنمية المستدامة عملنا اتجاهًا مشتركًا.', [
   'وهي لغة مشتركة مع شركائنا. نسهم في تحقيقها عبر توليد الأدلة وتعزيز القدرات وجمع من يستطيعون تحويل الأهداف إلى نتائج على أرض الواقع.'], 3826, 'أهداف التنمية المستدامة السبعة عشر'),
  sdgs('أين نسهم أكثر', 'خمسة أهداف في قلب عملنا.', [
   ('04', '#C5192D', 'التعليم الجيد', 'تدريب وتعلّم يبنيان المهارات والمعرفة.'),
   ('05', '#FF3A21', 'المساواة بين الجنسين', 'تمكين المرأة في القيادة وريادة الأعمال.'),
   ('08', '#A21942', 'العمل اللائق ونمو الاقتصاد', 'بحوث ومهارات تدعم سبل العيش والتوظيف.'),
   ('16', '#00689D', 'السلام والعدل والمؤسسات القوية', 'أدلة وحوار يقوّيان الحوكمة والمؤسسات.'),
   ('17', '#19486A', 'عقد الشراكات لتحقيق الأهداف', 'ابتكار مشترك بين الأكاديميا وقطاع التنمية والمجتمعات المحلية.')]),
  strip([(4401, 'المرأة في القيادة'), (3299, 'التعلّم والمهارات'), (4064, 'الحوار والمؤسسات')])))

page('objectives',
 wrap(intro('Our core objectives', 'Development works best when evidence, policy and communities move together.', [
   'Our objectives turn that conviction into practice.'], 3887, 'Community dialogue'),
  cards('Our objectives', 'Five commitments that guide our work.', [
   ('', 'Generate evidence', 'Produce rigorous, locally grounded research that informs policy and practice.'),
   ('', 'Strengthen human capacity', 'Build skills and knowledge through training, learning and mentorship.'),
   ('', 'Bridge policy and action', 'Translate findings into recommendations decision-makers can use.'),
   ('', 'Foster co-creation', 'Bring academia, the development sector and communities together.'),
   ('', 'Share knowledge', 'Publish and disseminate research so that it reaches those who need it.')], cols=3, numbered=True)),
 wrap(intro('أهدافنا الأساسية', 'تنجح التنمية حين تتحرك الأدلة والسياسات والمجتمعات معًا.', [
   'وتحوّل أهدافنا هذه القناعة إلى ممارسة.'], 3887, 'حوار مجتمعي'),
  cards('أهدافنا', 'خمسة التزامات توجّه عملنا.', [
   ('', 'توليد الأدلة', 'إنتاج بحوث دقيقة ومتجذّرة محليًا تُثري السياسات والممارسات.'),
   ('', 'تعزيز القدرات البشرية', 'بناء المهارات والمعرفة عبر التدريب والتعلّم والإرشاد.'),
   ('', 'ربط السياسات بالعمل', 'تحويل النتائج إلى توصيات يستطيع صنّاع القرار استخدامها.'),
   ('', 'تعزيز الابتكار المشترك', 'جمع الأكاديميا وقطاع التنمية والمجتمعات معًا.'),
   ('', 'تبادل المعرفة', 'نشر البحوث لتصل إلى من يحتاجونها.')], cols=3, numbered=True)))

page('pillars',
 wrap(intro('Strategic pillars', 'Four pillars organise everything we do.', [
   'From the questions we research to the partnerships we build.'], 4250, 'Partners in a working session'),
  cards('Our pillars', 'The foundations of our strategy.', [
   ('ph-magnifying-glass', 'Research and evidence', 'Rigorous, locally grounded research that responds to real development questions.'),
   ('ph-chalkboard-teacher', 'Learning and capacity development', 'Training and knowledge sharing that strengthen people and institutions.'),
   ('ph-bank', 'Policy engagement', 'Dialogue with policy-makers so that evidence shapes decisions.'),
   ('ph-handshake', 'Partnership and co-creation', 'Collaboration among academia, the development sector and local communities.')], cols=2)),
 wrap(intro('الركائز الاستراتيجية', 'أربع ركائز تنظّم كل ما نقوم به.', [
   'من الأسئلة التي نبحثها إلى الشراكات التي نبنيها.'], 4250, 'شركاء في جلسة عمل'),
  cards('ركائزنا', 'أسس استراتيجيتنا.', [
   ('ph-magnifying-glass', 'البحث والأدلة', 'بحوث دقيقة ومتجذّرة محليًا تستجيب لأسئلة التنمية الفعلية.'),
   ('ph-chalkboard-teacher', 'التعلّم وتنمية القدرات', 'تدريب وتبادل معرفة يقوّيان الأفراد والمؤسسات.'),
   ('ph-bank', 'التأثير في السياسات', 'حوار مع صنّاع السياسات لتصوغ الأدلة القرارات.'),
   ('ph-handshake', 'الشراكة والابتكار المشترك', 'تعاون بين الأكاديميا وقطاع التنمية والمجتمعات المحلية.')], cols=2)))

# ---------------------------------------------------------------- Programs
def program(key, eye_en, eye_ar, lead_en, lead_ar, p_en, p_ar, img, alt_en, alt_ar, h_en, h_ar, items_en, items_ar, q_en, q_ar, strip_en, strip_ar):
    page(key,
     wrap(intro(eye_en, lead_en, p_en, img, alt_en), split(strip_en[0][0], strip_en[0][1], 'What we do', h_en, items_en),
          band(q_en, [(t, p) for _, t, p in items_en][:0] or []) if False else '',
          strip(strip_en[1:]), cta('Work with us on this programme', 'Partner with us, commission research or join our network of practitioners.', 'Partner with us →', 'strategic')),
     wrap(intro(eye_ar, lead_ar, p_ar, img, alt_ar), split(strip_ar[0][0], strip_ar[0][1], 'ما نقوم به', h_ar, items_ar),
          strip(strip_ar[1:]), cta('اعمل معنا في هذا البرنامج', 'كن شريكًا لنا، أو كلّفنا ببحث، أو انضم إلى شبكة الممارسين لدينا.', 'كن شريكًا ←', 'strategic')))

program('rnd', 'Research & development', 'البحث والتطوير',
 'We generate, share and apply evidence to address complex development challenges.', 'نولّد الأدلة ونتبادلها ونطبّقها لمعالجة تحديات التنمية المعقّدة.',
 ['By integrating scientific research, indigenous knowledge and community experiences, we promote learning that informs policy, practice and sustainable solutions.',
  'We design and implement multidisciplinary, participatory research projects that generate evidence for policies, programmes and community-driven solutions.'],
 ['من خلال دمج البحث العلمي والمعرفة المحلية وتجارب المجتمعات، نعزّز تعلّمًا يُثري السياسات والممارسات والحلول المستدامة.',
  'نصمّم وننفّذ مشاريع بحثية تشاركية متعددة التخصصات تولّد أدلة للسياسات والبرامج والحلول المجتمعية.'],
 3864, 'Participants at a research validation session', 'مشاركون في جلسة للتحقق من نتائج بحث',
 'Research that starts with the right questions.', 'بحوث تبدأ بالأسئلة الصحيحة.',
 [('ph-users-three', 'Participatory research', 'Communities help define questions, collect data and interpret findings.'),
  ('ph-chart-bar', 'Policy-relevant evidence', 'Studies designed around the decisions governments and partners must make.'),
  ('ph-arrows-left-right', 'Knowledge exchange', 'Findings shared through briefs, dialogues and training, not left on the shelf.')],
 [('ph-users-three', 'البحث التشاركي', 'تشارك المجتمعات في تحديد الأسئلة وجمع البيانات وتفسير النتائج.'),
  ('ph-chart-bar', 'أدلة ذات صلة بالسياسات', 'دراسات تُصمَّم حول القرارات التي يتخذها الحكومات والشركاء.'),
  ('ph-arrows-left-right', 'تبادل المعرفة', 'نشارك النتائج عبر الموجزات والحوارات والتدريب، لا نتركها على الرفوف.')],
 '', '', [(4250, 'A working session with partners'), (3299, 'Learning and research skills'), (4048, 'Regional knowledge exchange')],
 [(4250, 'جلسة عمل مع الشركاء'), (3299, 'التعلّم ومهارات البحث'), (4048, 'تبادل المعرفة الإقليمي')])

program('community', 'Community development', 'تنمية المجتمع',
 'Inclusive Communities & Local Resilience is NIKON’s direct-impact arm.', 'برنامج المجتمعات الشاملة والصمود المحلي هو ذراع الأثر المباشر لمعهد نيكون.',
 ['It ensures that vulnerable, marginalised and grassroots communities are not just subjects of research, but active architects of their own economic and climate resilience.',
  'We prioritise locally driven approaches that build on existing assets, knowledge and capacities.'],
 ['يضمن ألا تكون المجتمعات الهشّة والمهمّشة والقاعدية مجرد موضوع للبحث، بل صانعة فاعلة لقدرتها على الصمود الاقتصادي والمناخي.',
  'نعطي الأولوية للنُّهج المحلية التي تبني على الأصول والمعارف والقدرات القائمة.'],
 3869, 'Community members during a consultation', 'أفراد من المجتمع أثناء مشاورة',
 'Communities leading their own development.', 'مجتمعات تقود تنميتها بنفسها.',
 [('ph-map-trifold', 'Community-led planning', 'Local priorities, identified and ranked by the people who live with them.'),
  ('ph-hand-heart', 'Inclusion', 'Women, youth, displaced people and minorities at the centre of decisions.'),
  ('ph-plant', 'Local resilience', 'Building on existing assets to withstand economic and climate shocks.')],
 [('ph-map-trifold', 'التخطيط بقيادة المجتمع', 'أولويات محلية يحددها ويرتّبها من يعيشونها.'),
  ('ph-hand-heart', 'الإدماج', 'النساء والشباب والنازحون والأقليات في قلب القرارات.'),
  ('ph-plant', 'الصمود المحلي', 'البناء على الأصول القائمة لمواجهة الصدمات الاقتصادية والمناخية.')],
 '', '', [(3887, 'Dialogue under the trees'), (3862, 'Women in the field'), (3903, 'A community workshop')],
 [(3887, 'حوار تحت الأشجار'), (3862, 'نساء في الميدان'), (3903, 'ورشة مجتمعية')])

program('governance', 'Governance', 'الحوكمة',
 'We strengthen institutions, leadership and participatory decision-making.', 'نقوّي المؤسسات والقيادة وصنع القرار التشاركي.',
 ['Our work empowers communities and promotes accountability through inclusive democracy.',
  'We support inclusive systems that enable diverse voices, particularly those of women, youth and marginalised groups, to shape development priorities.'],
 ['يمكّن عملنا المجتمعات ويعزّز المساءلة عبر الديمقراطية الشاملة.',
  'ندعم أنظمة شاملة تتيح لأصوات متنوعة، ولا سيما النساء والشباب والفئات المهمّشة، المشاركة في تحديد أولويات التنمية.'],
 4063, 'A policy meeting in session', 'اجتماع سياساتي منعقد',
 'Institutions that work for everyone.', 'مؤسسات تعمل لصالح الجميع.',
 [('ph-buildings', 'Institutional strengthening', 'Evidence and training that help public institutions plan and deliver.'),
  ('ph-megaphone', 'Inclusive participation', 'Opening decision-making to women, youth and marginalised groups.'),
  ('ph-scales', 'Accountability and dialogue', 'Platforms where citizens and leaders meet around shared evidence.')],
 [('ph-buildings', 'تقوية المؤسسات', 'أدلة وتدريب يساعدان المؤسسات العامة على التخطيط والتنفيذ.'),
  ('ph-megaphone', 'المشاركة الشاملة', 'فتح صنع القرار أمام النساء والشباب والفئات المهمّشة.'),
  ('ph-scales', 'المساءلة والحوار', 'منصات يلتقي فيها المواطنون والقادة حول أدلة مشتركة.')],
 '', '', [(4064, 'Political dialogue'), (3245, 'Women in leadership'), (3308, 'Parliamentary engagement')],
 [(4064, 'الحوار السياسي'), (3245, 'المرأة في القيادة'), (3308, 'العمل مع البرلمان')])

program('climate', 'Climate resilience', 'القدرة على مواجهة تغيّر المناخ',
 'Climate change is intensifying droughts, floods and resource competition across the Horn of Africa.', 'يفاقم تغيّر المناخ الجفاف والفيضانات والتنافس على الموارد في القرن الأفريقي.',
 ['We support locally led adaptation strategies that strengthen resilience, protect ecosystems and improve the sustainable management of natural resources.'],
 ['ندعم استراتيجيات تكيّف بقيادة محلية تعزّز الصمود وتحمي النظم البيئية وتحسّن الإدارة المستدامة للموارد الطبيعية.'],
 3862, 'Women farmers in the field', 'مزارعات في الحقل',
 'Adaptation led by those most affected.', 'تكيّف يقوده الأكثر تأثرًا.',
 [('ph-cloud-rain', 'Locally led adaptation', 'Communities design and own their responses to drought and flood.'),
  ('ph-tree', 'Natural resource management', 'Protecting land, water and pasture for the long term.'),
  ('ph-chart-line-up', 'Climate evidence', 'Data and research that guide preparedness and investment.')],
 [('ph-cloud-rain', 'تكيّف بقيادة محلية', 'تصمّم المجتمعات استجاباتها للجفاف والفيضانات وتمتلكها.'),
  ('ph-tree', 'إدارة الموارد الطبيعية', 'حماية الأرض والمياه والمراعي على المدى البعيد.'),
  ('ph-chart-line-up', 'أدلة مناخية', 'بيانات وبحوث توجّه الاستعداد والاستثمار.')],
 '', '', [(3313, 'Climate adaptation planning'), (3889, 'Communities together'), (3887, 'Local consultation')],
 [(3313, 'التخطيط للتكيّف المناخي'), (3889, 'مجتمعات متكاتفة'), (3887, 'تشاور محلي')])

program('economic', 'Economic empowerment', 'التمكين الاقتصادي',
 'We co-create practical solutions that improve livelihoods and expand economic opportunity.', 'نبتكر مع المجتمعات حلولًا عملية تحسّن سبل العيش وتوسّع الفرص الاقتصادية.',
 ['Our community empowerment and sustainable livelihoods work strengthens long-term wellbeing, prioritising locally driven approaches that build on existing assets, knowledge and capacities.'],
 ['يعزّز عملنا في تمكين المجتمعات وسبل العيش المستدامة الرفاه على المدى البعيد، مع إعطاء الأولوية للنُّهج المحلية التي تبني على الأصول والمعارف والقدرات القائمة.'],
 3241, 'Women entrepreneurs at a NIKON event', 'رائدات أعمال في فعالية لمعهد نيكون',
 'Livelihoods that last.', 'سبل عيش مستدامة.',
 [('ph-storefront', 'Livelihoods', 'Practical support that grows household incomes and resilience.'),
  ('ph-gender-female', 'Women and youth enterprise', 'Advancing women and young people in business and leadership.'),
  ('ph-toolbox', 'Skills for work', 'Training matched to the jobs and markets that exist locally.')],
 [('ph-storefront', 'سبل العيش', 'دعم عملي ينمّي دخل الأسر وقدرتها على الصمود.'),
  ('ph-gender-female', 'ريادة النساء والشباب', 'تمكين النساء والشباب في الأعمال والقيادة.'),
  ('ph-toolbox', 'مهارات للعمل', 'تدريب يناسب الوظائف والأسواق المتاحة محليًا.')],
 '', '', [(3300, 'A small business in Mogadishu'), (4405, 'Women in entrepreneurship'), (3299, 'Building skills')],
 [(3300, 'مشروع صغير في مقديشو'), (4405, 'المرأة وريادة الأعمال'), (3299, 'بناء المهارات')])

program('peace', 'Peacebuilding', 'بناء السلام',
 'Lasting development depends on peaceful, inclusive and resilient societies.', 'التنمية الدائمة تقوم على مجتمعات مسالمة وشاملة وقادرة على الصمود.',
 ['We facilitate dialogue, strengthen relationships and support collaborative approaches that foster trust, prevent conflict and promote social cohesion.'],
 ['نيسّر الحوار ونقوّي العلاقات وندعم النُّهج التعاونية التي تبني الثقة وتمنع النزاعات وتعزّز التماسك الاجتماعي.'],
 3883, 'Elders and community members in dialogue', 'شيوخ وأفراد من المجتمع في حوار',
 'Trust built through dialogue.', 'ثقة تُبنى بالحوار.',
 [('ph-chats-circle', 'Dialogue and reconciliation', 'Safe spaces where communities resolve differences together.'),
  ('ph-users-four', 'Social cohesion', 'Strengthening relationships across clans, generations and groups.'),
  ('ph-hand-heart', 'Inclusive participation', 'Women and youth as active builders of peace.')],
 [('ph-chats-circle', 'الحوار والمصالحة', 'مساحات آمنة تحلّ فيها المجتمعات خلافاتها معًا.'),
  ('ph-users-four', 'التماسك الاجتماعي', 'تقوية العلاقات بين العشائر والأجيال والفئات.'),
  ('ph-hand-heart', 'المشاركة الشاملة', 'النساء والشباب بناةٌ فاعلون للسلام.')],
 '', '', [(3889, 'A circle of peace'), (3900, 'Community dialogue'), (3904, 'Coming together')],
 [(3889, 'دائرة سلام'), (3900, 'حوار مجتمعي'), (3904, 'التلاقي')])

# ---------------------------------------------------------------- Training
def training(key, eye, lead, paras, img, alt, h2, items, strip_items, ar):
    if ar:
        page_ar[key] = wrap(intro(eye, lead, paras, img, alt), cards('ما نقدّمه', h2, items, cols=3), strip(strip_items),
                            cta('هل تريد هذا التدريب لفريقك؟', 'تواصل معنا لتصميم برنامج يناسب مؤسستك.', 'تواصل معنا ←', 'contact'))
    else:
        page_en[key] = wrap(intro(eye, lead, paras, img, alt), cards('What we offer', h2, items, cols=3), strip(strip_items),
                            cta('Want this training for your team?', 'Get in touch and we will design a programme around your organisation.', 'Contact us →', 'contact'))
page_en, page_ar = {}, {}
T = [
 ('proTraining', 3299,
  ('Professional training', 'Practical training that builds the professional skills practitioners and institutions need.', ['Our courses combine research know-how with field experience, so participants leave with skills they can use the next day.'], 'Participants in a training session',
   'Courses built around real work.', [('ph-chart-bar', 'Research methods', 'Data collection, analysis and research ethics.'), ('ph-kanban', 'Project management', 'Planning, monitoring, evaluation and learning.'), ('ph-presentation-chart', 'Policy & communication', 'Turning findings into briefs and presentations.')],
   [(4394, 'Students at NIKON'), (3864, 'A learning session'), (1669, 'Classroom learning')]),
  ('التدريب المهني', 'تدريب عملي يبني المهارات المهنية التي يحتاجها الممارسون والمؤسسات.', ['تجمع دوراتنا بين الخبرة البحثية والخبرة الميدانية، ليغادر المشاركون بمهارات يستخدمونها في اليوم التالي.'], 'مشاركون في جلسة تدريبية',
   'دورات مبنية حول العمل الفعلي.', [('ph-chart-bar', 'مناهج البحث', 'جمع البيانات وتحليلها وأخلاقيات البحث.'), ('ph-kanban', 'إدارة المشاريع', 'التخطيط والرصد والتقييم والتعلّم.'), ('ph-presentation-chart', 'السياسات والتواصل', 'تحويل النتائج إلى موجزات وعروض.')],
   [(4394, 'طلاب في نيكون'), (3864, 'جلسة تعلّم'), (1669, 'التعلّم في الفصل')])),
 ('capacity', 3864,
  ('Capacity building', 'Strengthening organisations and public institutions so they can plan, deliver and learn.', ['We work alongside teams over time, combining training, coaching and practical tools that stay with the organisation.'], 'A capacity building session',
   'Stronger organisations, stronger results.', [('ph-buildings', 'Institutional systems', 'Policies, processes and data systems that work.'), ('ph-user-focus', 'Leadership', 'Coaching for managers and emerging leaders.'), ('ph-arrows-clockwise', 'Learning culture', 'Monitoring, evaluation and continuous improvement.')],
   [(4250, 'Working with partners'), (3303, 'Regional cooperation'), (4048, 'Peer learning')]),
  ('بناء القدرات', 'تقوية المنظمات والمؤسسات العامة لتتمكن من التخطيط والتنفيذ والتعلّم.', ['نعمل مع الفرق على المدى الطويل، ونجمع بين التدريب والإرشاد والأدوات العملية التي تبقى مع المؤسسة.'], 'جلسة لبناء القدرات',
   'منظمات أقوى، نتائج أفضل.', [('ph-buildings', 'الأنظمة المؤسسية', 'سياسات وإجراءات وأنظمة بيانات فعّالة.'), ('ph-user-focus', 'القيادة', 'إرشاد للمديرين والقادة الناشئين.'), ('ph-arrows-clockwise', 'ثقافة التعلّم', 'الرصد والتقييم والتحسين المستمر.')],
   [(4250, 'العمل مع الشركاء'), (3303, 'التعاون الإقليمي'), (4048, 'التعلّم من الأقران')])),
 ('workshops', 3903,
  ('Workshops', 'Interactive workshops that bring stakeholders together to learn, share and solve problems.', ['From validation workshops for research findings to community consultations, our sessions are designed for participation, not lectures.'], 'Participants at a workshop',
   'Formats that get people talking.', [('ph-check-square-offset', 'Validation workshops', 'Testing research findings with the people they concern.'), ('ph-users-three', 'Multi-stakeholder dialogues', 'Government, partners and communities at one table.'), ('ph-lightbulb', 'Design sessions', 'Co-creating solutions and action plans.')],
   [(3887, 'A community workshop'), (4396, 'Women’s day workshop'), (3864, 'Group discussion')]),
  ('ورش العمل', 'ورش عمل تفاعلية تجمع الأطراف المعنية للتعلّم وتبادل الخبرات وحل المشكلات.', ['من ورش التحقق من نتائج البحوث إلى المشاورات المجتمعية، تُصمَّم جلساتنا للمشاركة لا للتلقين.'], 'مشاركون في ورشة عمل',
   'صيغ تشجّع على الحوار.', [('ph-check-square-offset', 'ورش التحقق', 'اختبار نتائج البحوث مع من تعنيهم.'), ('ph-users-three', 'حوارات متعددة الأطراف', 'الحكومة والشركاء والمجتمعات حول طاولة واحدة.'), ('ph-lightbulb', 'جلسات التصميم', 'ابتكار الحلول وخطط العمل معًا.')],
   [(3887, 'ورشة مجتمعية'), (4396, 'ورشة اليوم العالمي للمرأة'), (3864, 'نقاش جماعي')])),
 ('fellowships', 4402,
  ('Fellowships', 'We support leadership development, professional exchanges and cross-learning opportunities.', ['Our fellowships and exchange programmes enhance regional expertise and foster long-term collaboration across Somalia and the Horn of Africa.'], 'Students at school in Mogadishu',
   'Investing in the next generation.', [('ph-graduation-cap', 'Research fellowships', 'Time, mentorship and support for emerging researchers.'), ('ph-airplane-tilt', 'Professional exchanges', 'Learning visits with partner institutions in the region.'), ('ph-user-circle-plus', 'Leadership development', 'Building the leaders Somalia’s institutions need.')],
   [(4400, 'Young learners'), (3299, 'Studying for the future'), (4405, 'Women leaders')]),
  ('الزمالات', 'ندعم تطوير القيادات والتبادل المهني وفرص التعلّم المتبادل.', ['تعزّز برامج الزمالات والتبادل لدينا الخبرات الإقليمية وتبني تعاونًا طويل الأمد في الصومال والقرن الأفريقي.'], 'طلاب في مدرسة بمقديشو',
   'نستثمر في الجيل القادم.', [('ph-graduation-cap', 'زمالات بحثية', 'وقت وإرشاد ودعم للباحثين الناشئين.'), ('ph-airplane-tilt', 'التبادل المهني', 'زيارات تعلّم مع مؤسسات شريكة في المنطقة.'), ('ph-user-circle-plus', 'تطوير القيادات', 'إعداد القادة الذين تحتاجهم مؤسسات الصومال.')],
   [(4400, 'متعلمون صغار'), (3299, 'الدراسة من أجل المستقبل'), (4405, 'قيادات نسائية')])),
 ('elearning', 1663,
  ('E-learning', 'Online courses and learning resources, available wherever our learners are.', ['Distance and blended learning extend our training to practitioners across Somalia and the region, at a pace that fits their work.'], 'Students learning in a classroom',
   'Learning without distance.', [('ph-monitor-play', 'Online courses', 'Self-paced modules on research, data and policy.'), ('ph-video-conference', 'Live sessions', 'Webinars and virtual classrooms with our experts.'), ('ph-books', 'Learning resources', 'Guides, toolkits and readings to download.')],
   [(1669, 'Learners in Mogadishu'), (3299, 'Exam preparation'), (4394, 'Students at NIKON')]),
  ('التعلّم الإلكتروني', 'دورات وموارد تعليمية عبر الإنترنت متاحة للمتعلمين أينما كانوا.', ['يوسّع التعلّم عن بُعد والتعلّم المدمج نطاق تدريبنا ليصل إلى الممارسين في الصومال والمنطقة، وبوتيرة تناسب عملهم.'], 'طالبات يتعلمن في الفصل',
   'تعلّم بلا مسافات.', [('ph-monitor-play', 'دورات عبر الإنترنت', 'وحدات ذاتية الوتيرة في البحث والبيانات والسياسات.'), ('ph-video-conference', 'جلسات مباشرة', 'ندوات وفصول افتراضية مع خبرائنا.'), ('ph-books', 'موارد تعليمية', 'أدلة وحقائب أدوات وقراءات للتحميل.')],
   [(1669, 'متعلمات في مقديشو'), (3299, 'الاستعداد للامتحانات'), (4394, 'طلاب في نيكون')])),
]
for key, img, en, ar in T:
    training(key, en[0], en[1], en[2], img, en[3], en[4], en[5], en[6], False)
    training(key, ar[0], ar[1], ar[2], img, ar[3], ar[4], ar[5], ar[6], True)
    page(key, page_en[key], page_ar[key])

# ---------------------------------------------------------------- Knowledge
page('briefs',
 wrap(intro('Policy briefs', 'Short, practical recommendations for decision-makers.', ['Our briefs and papers distil research into what matters for policy, written for the people who make and implement decisions.'], 3308, 'A parliamentary session'),
  cards('Formats', 'Evidence in the right format.', [('ph-file-text', 'Policy briefs', 'Short, practical recommendations for decision-makers.'), ('ph-note-pencil', 'Working papers', 'Research in progress, shared early for discussion.'), ('ph-article', 'Research papers', 'Completed studies with full methods and findings.')]),
  cta('Looking for our books?', 'Books and long-form studies can be previewed online.', 'Publications →', 'publications')),
 wrap(intro('موجزات السياسات', 'توصيات قصيرة وعملية لصنّاع القرار.', ['تلخّص موجزاتنا وأوراقنا البحوث فيما يهم السياسات، وتُكتب لمن يتخذون القرارات وينفّذونها.'], 3308, 'جلسة برلمانية'),
  cards('الصيغ', 'أدلة بالصيغة المناسبة.', [('ph-file-text', 'موجزات السياسات', 'توصيات قصيرة وعملية لصنّاع القرار.'), ('ph-note-pencil', 'أوراق العمل', 'بحوث قيد الإنجاز تُنشر مبكرًا للنقاش.'), ('ph-article', 'الأوراق البحثية', 'دراسات مكتملة بمنهجياتها ونتائجها.')]),
  cta('هل تبحث عن كتبنا؟', 'يمكن معاينة الكتب والدراسات المطوّلة عبر الإنترنت.', 'المنشورات ←', 'publications')))

page('resReports',
 wrap(intro('Research reports', 'Findings from our studies, assessments and fieldwork.', ['Our reports document what we learn, so that partners, policy-makers and communities can act on it.'], 3303, 'A regional conference'),
  cards('Report types', 'What you will find here.', [('ph-magnifying-glass', 'Research reports', 'Findings from our studies and fieldwork.'), ('ph-clipboard-text', 'Assessments and evaluations', 'Evidence on what works, for partners and programmes.'), ('ph-calendar-check', 'Annual reports', 'A yearly account of the Institute’s work.')])),
 wrap(intro('التقارير البحثية', 'نتائج دراساتنا وتقييماتنا وعملنا الميداني.', ['توثّق تقاريرنا ما نتعلّمه، ليتمكن الشركاء وصنّاع السياسات والمجتمعات من البناء عليه.'], 3303, 'مؤتمر إقليمي'),
  cards('أنواع التقارير', 'ما ستجده هنا.', [('ph-magnifying-glass', 'تقارير بحثية', 'نتائج دراساتنا وعملنا الميداني.'), ('ph-clipboard-text', 'تقييمات', 'أدلة على ما ينجح، للشركاء والبرامج.'), ('ph-calendar-check', 'تقارير سنوية', 'حصيلة سنوية لعمل المعهد.')])))

page('studies',
 wrap(intro('Studies', 'In-depth studies on development questions facing Somalia and the Horn of Africa.', ['Our studies go deeper than a single project, examining how education, livelihoods, governance and climate interact in people’s lives.'], 1803, 'A forum on economic management and governance'),
  tcards('Study areas', 'Questions we study in depth.', [(1669, 'Education', 'Access, quality and the economics of schooling.'), (3300, 'Livelihoods', 'Incomes, enterprise and decent work.'), (3313, 'Climate', 'Adaptation, resources and resilience.')])),
 wrap(intro('الدراسات', 'دراسات معمّقة حول قضايا التنمية في الصومال والقرن الأفريقي.', ['تتعمّق دراساتنا أبعد من مشروع واحد، فتبحث في تفاعل التعليم وسبل العيش والحوكمة والمناخ في حياة الناس.'], 1803, 'منتدى حول الإدارة الاقتصادية والحوكمة'),
  tcards('مجالات الدراسة', 'أسئلة ندرسها بعمق.', [(1669, 'التعليم', 'الوصول والجودة واقتصاديات التعليم.'), (3300, 'سبل العيش', 'الدخل وريادة الأعمال والعمل اللائق.'), (3313, 'المناخ', 'التكيّف والموارد والصمود.')])))

page('sharing',
 wrap(intro('Knowledge sharing', 'We translate research into recommendations that influence decisions.', ['We facilitate policy discussions at local, national and regional levels.', 'As a learning and evidence-making organisation, NIKON promotes equitable research and partnerships that inform humanitarian and development programming aligned with the 17 SDGs.'], 4064, 'A national policy dialogue'),
  cards('How we share', 'From findings to influence.', [('ph-chats', 'Policy dialogues', 'Convening decision-makers around evidence.'), ('ph-microphone-stage', 'Forums and events', 'Public discussions, panels and launches.'), ('ph-share-network', 'Open publications', 'Briefs, papers and books available to all.')]),
  strip([(4048, 'Regional cooperation'), (4401, 'Women’s day forum'), (3308, 'Engaging institutions')])),
 wrap(intro('تبادل المعرفة', 'نحوّل البحوث إلى توصيات تؤثّر في القرارات.', ['ننظّم حوارات سياساتية على المستويات المحلية والوطنية والإقليمية.', 'بوصفنا منظمة للتعلّم وصناعة الأدلة، يعزّز معهد نيكون البحث المنصف والشراكات التي تُثري البرامج الإنسانية والتنموية بما يتوافق مع الأهداف السبعة عشر.'], 4064, 'حوار وطني حول السياسات'),
  cards('كيف نتبادل المعرفة', 'من النتائج إلى التأثير.', [('ph-chats', 'حوارات السياسات', 'جمع صنّاع القرار حول الأدلة.'), ('ph-microphone-stage', 'منتديات وفعاليات', 'نقاشات عامة وجلسات وإطلاقات.'), ('ph-share-network', 'منشورات مفتوحة', 'موجزات وأوراق وكتب متاحة للجميع.')]),
  strip([(4048, 'التعاون الإقليمي'), (4401, 'منتدى اليوم العالمي للمرأة'), (3308, 'العمل مع المؤسسات')])))

# ---------------------------------------------------------------- Partnerships (form appended separately)
page('strategic',
 wrap(intro('Strategic partnerships', 'Long-term partnerships that strengthen knowledge ecosystems.', ['We build strategic partnerships with universities, research institutions, governments, civil society organisations and development partners.'], 4064, 'Partners at a high-level meeting'),
  cards('Why partner with us', 'What a partnership brings.', [('ph-map-pin', 'Local reach', 'Trusted relationships with communities across Somalia.'), ('ph-flask', 'Research capacity', 'Rigorous methods and participatory approaches.'), ('ph-handshake', 'Co-creation', 'Shared design, delivery and learning.')])),
 wrap(intro('الشراكات الاستراتيجية', 'شراكات طويلة الأمد تقوّي منظومات المعرفة.', ['نبني شراكات استراتيجية مع الجامعات ومؤسسات البحث والحكومات ومنظمات المجتمع المدني وشركاء التنمية.'], 4064, 'شركاء في اجتماع رفيع المستوى'),
  cards('لماذا تتشارك معنا', 'ما تضيفه الشراكة.', [('ph-map-pin', 'وصول محلي', 'علاقات موثوقة مع المجتمعات في أنحاء الصومال.'), ('ph-flask', 'قدرات بحثية', 'مناهج دقيقة ونُهج تشاركية.'), ('ph-handshake', 'ابتكار مشترك', 'تصميم وتنفيذ وتعلّم مشترك.')])))

page('institutional',
 wrap(intro('Institutional collaboration', 'Working with universities, research centres and public institutions.', ['Together we strengthen research, teaching and learning, and connect academic knowledge with the realities of development.'], 3299, 'Students in an examination hall'),
  cards('Ways to collaborate', 'Collaboration formats.', [('ph-flask', 'Joint research', 'Co-designed studies and shared data.'), ('ph-chalkboard-teacher', 'Training & courses', 'Joint programmes for students and staff.'), ('ph-swap', 'Exchanges', 'Visits and placements between institutions.'), ('ph-book-open', 'Joint publications', 'Papers and books written together.')], cols=4)),
 wrap(intro('التعاون المؤسسي', 'نعمل مع الجامعات ومراكز البحث والمؤسسات العامة.', ['نقوّي معًا البحث والتعليم والتعلّم، ونربط المعرفة الأكاديمية بواقع التنمية.'], 3299, 'طلاب في قاعة امتحانات'),
  cards('صيغ التعاون', 'أشكال التعاون.', [('ph-flask', 'بحث مشترك', 'دراسات مصمّمة معًا وبيانات مشتركة.'), ('ph-chalkboard-teacher', 'تدريب ودورات', 'برامج مشتركة للطلاب والموظفين.'), ('ph-swap', 'تبادل', 'زيارات وتنسيب بين المؤسسات.'), ('ph-book-open', 'منشورات مشتركة', 'أوراق وكتب تُكتب معًا.')], cols=4)))

page('devPartners',
 wrap(intro('Development partners', 'We work alongside UN agencies, international organisations, research centres and government institutions.', ['Together we design, deliver and evaluate programmes grounded in evidence and local knowledge.'], 4048, 'Development partners at a regional meeting'),
  cards('Stakeholders we engage', 'Who we work with.', [('ph-globe-hemisphere-east', 'UN agencies', 'UNHCR, UNICEF, WFP, UN Women, IOM, ILO and WHO.'), ('ph-hand-coins', 'Funds and international partners', 'GCERF, the Global Community Engagement and Resilience Fund.'), ('ph-magnifying-glass', 'Research and policy centres', 'CSEP, Centre for Social and Economic Progress; ACET, African Center for Economic Transformation.'), ('ph-bank', 'Government', 'Ministries and institutions of the Federal Government of Somalia.')], cols=2)),
 wrap(intro('شركاء التنمية', 'نعمل جنبًا إلى جنب مع وكالات الأمم المتحدة والمنظمات الدولية ومراكز البحث والمؤسسات الحكومية.', ['نصمّم معًا البرامج وننفّذها ونقيّمها استنادًا إلى الأدلة والمعرفة المحلية.'], 4048, 'شركاء التنمية في اجتماع إقليمي'),
  cards('الأطراف التي نتعامل معها', 'مع من نعمل.', [('ph-globe-hemisphere-east', 'وكالات الأمم المتحدة', 'المفوضية السامية لشؤون اللاجئين، اليونيسف، برنامج الأغذية العالمي، هيئة الأمم المتحدة للمرأة، المنظمة الدولية للهجرة، منظمة العمل الدولية ومنظمة الصحة العالمية.'), ('ph-hand-coins', 'الصناديق والشركاء الدوليون', 'الصندوق العالمي للمشاركة المجتمعية والصمود (GCERF).'), ('ph-magnifying-glass', 'مراكز البحث والسياسات', 'مركز التقدم الاجتماعي والاقتصادي (CSEP)؛ المركز الأفريقي للتحول الاقتصادي (ACET).'), ('ph-bank', 'الحكومة', 'وزارات ومؤسسات الحكومة الفيدرالية الصومالية.')], cols=2)))

page('joinUs',
 wrap(intro('Join us', 'There is a place for you in our work.', ['Researchers, practitioners, volunteers and organisations: help us make evidence matter for local change.'], 4405, 'Participants at a NIKON event'),
  cards('Ways to join', 'Choose how you want to contribute.', [('ph-flask', 'Researchers', 'Join studies and co-author publications.'), ('ph-chalkboard-teacher', 'Trainers', 'Share your expertise in our courses.'), ('ph-hand-heart', 'Volunteers', 'Support events, outreach and data collection.'), ('ph-student', 'Interns', 'Learn research and development work in practice.')], cols=4)),
 wrap(intro('انضم إلينا', 'لك مكان في عملنا.', ['باحثون وممارسون ومتطوعون ومنظمات: ساعدونا لنجعل للأدلة أثرًا في التغيير المحلي.'], 4405, 'مشاركات في فعالية لمعهد نيكون'),
  cards('طرق الانضمام', 'اختر كيف تريد أن تسهم.', [('ph-flask', 'باحثون', 'المشاركة في الدراسات وتأليف المنشورات.'), ('ph-chalkboard-teacher', 'مدرّبون', 'شارك خبرتك في دوراتنا.'), ('ph-hand-heart', 'متطوعون', 'دعم الفعاليات والتواصل وجمع البيانات.'), ('ph-student', 'متدرّبون', 'تعلّم العمل البحثي والتنموي عمليًا.')], cols=4)))

# ---------------------------------------------------------------- Media
page('events',
 wrap(intro('Events', 'Conferences, panels, trainings and community events.', ['We host and support events that bring evidence, policy-makers and communities into the same room.'], 4401, 'International Women’s Day 2026 at NIKON'),
  split(4405, 'Speaker at International Women’s Day 2026', 'Featured event', 'International Women’s Day 2026.', [
   ('ph-calendar-blank', 'March 8, 2026', 'Mogadishu'), ('ph-gender-female', 'Women in leadership and entrepreneurship', 'A NIKON Institute event celebrating and advancing women leaders and entrepreneurs.'), ('ph-users-three', 'Rights, justice and action', 'Speakers, panels and networking for women across sectors.')]),
  strip([(3235, 'Participants'), (3245, 'Keynote'), (4403, 'The audience')])),
 wrap(intro('الفعاليات', 'مؤتمرات وجلسات نقاش ودورات تدريبية وفعاليات مجتمعية.', ['ننظّم ونستضيف فعاليات تجمع الأدلة وصنّاع السياسات والمجتمعات في مكان واحد.'], 4401, 'اليوم العالمي للمرأة 2026 في معهد نيكون'),
  split(4405, 'متحدثة في اليوم العالمي للمرأة 2026', 'فعالية مميزة', 'اليوم العالمي للمرأة 2026.', [
   ('ph-calendar-blank', '8 مارس 2026', 'مقديشو'), ('ph-gender-female', 'المرأة في القيادة وريادة الأعمال', 'فعالية لمعهد نيكون للاحتفاء بالقيادات النسائية ورائدات الأعمال وتمكينهن.'), ('ph-users-three', 'الحقوق والعدالة والعمل', 'متحدثات وجلسات نقاش وتواصل لنساء من مختلف القطاعات.')]),
  strip([(3235, 'المشاركات'), (3245, 'الكلمة الرئيسية'), (4403, 'الحضور')])))

page('stories',
 wrap(intro('Success stories', 'Stories of the people and communities whose lives our work has helped change.', ['Behind every study and training are people: women starting businesses, young people gaining skills, communities finding common ground.'], 3300, 'A woman running her shop'),
  strip([(3862, 'Women in the field'), (4405, 'Women leaders'), (4402, 'Young learners')]),
  cta('Have a story to share?', 'If our work has made a difference for you or your community, we would love to hear from you.', 'Share your story →', 'contact')),
 wrap(intro('قصص النجاح', 'قصص أشخاص ومجتمعات أسهم عملنا في تغيير حياتهم.', ['خلف كل دراسة وتدريب أناس: نساء يبدأن مشاريعهن، وشباب يكتسبون المهارات، ومجتمعات تجد أرضية مشتركة.'], 3300, 'امرأة تدير متجرها'),
  strip([(3862, 'نساء في الميدان'), (4405, 'قيادات نسائية'), (4402, 'متعلمون صغار')]),
  cta('هل لديك قصة تشاركها؟', 'إن أحدث عملنا فرقًا في حياتك أو مجتمعك، يسعدنا أن نسمع منك.', 'شارك قصتك ←', 'contact')))

if __name__ == '__main__':
    json.dump(PAGES, open('/tmp/pages_v2.json', 'w'), ensure_ascii=False)
    print(len(PAGES), 'pages')
