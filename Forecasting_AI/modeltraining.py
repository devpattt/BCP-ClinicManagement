import pandas as pd
import joblib
from sqlalchemy import create_engine

model = joblib.load("health_risk_model.pkl")

engine = create_engine('mysql+pymysql://root:@localhost:4306/bcp_sms3_cms')

query = "SELECT headache, dizziness, fatigue, fever FROM bcp_sms3_symptoms"
df = pd.read_sql(query, engine)

predictions = model.predict(df)

df['predicted_risk'] = predictions

risk_to_illness = {
    0: 'No illness',
    1: 'Common Cold',
    2: 'Flu',
    3: 'Migraine',
    4: 'panic disorder',
    5: 'vocal cord polyp',
    6: 'turner syndrome',
    7: 'cryptorchidism',
    8: 'poisoning due to ethylene glycol',
    9: 'atrophic vaginitis',
    10: 'fracture of the hand',
    11: 'cellulitis or abscess of mouth',
    12: 'eye alignment disorder',
    13: 'headache after lumbar puncture',
    14: 'pyloric stenosis',
    15: 'salivary gland disorder',
    16: 'osteochondrosis',
    17: 'injury to the knee',
    18: 'metabolic disorder',
    19: 'vaginitis',
    20: 'sick sinus syndrome',
    21: 'tinnitus of unknown cause',
    22: 'glaucoma',
    23: 'eating disorder',
    24: 'transient ischemic attack',
    25: 'pyelonephritis',
    26: 'rotator cuff injury',
    27: 'chronic pain disorder',
    28: 'problem during pregnancy',
    29: 'liver cancer',
    30: 'atelectasis',
    31: 'injury to the hand',
    32: 'choledocholithiasis',
    33: 'injury to the hip',
    34: 'cirrhosis',
    35: 'thoracic aortic aneurysm',
    36: 'subdural hemorrhage',
    37: 'diabetic retinopathy',
    38: 'fibromyalgia',
    39: 'ischemia of the bowel',
    40: 'fetal alcohol syndrome',
    41: 'peritonitis',
    42: 'injury to the abdomen',
    43: 'acute pancreatitis',
    44: 'thrombophlebitis',
    45: 'asthma',
    46: 'foreign body in the vagina',
    47: 'restless leg syndrome',
    48: 'emphysema',
    49: 'cysticercosis',
    50: 'induced abortion',
    51: 'teething syndrome',
    52: 'infectious gastroenteritis',
    53: 'acute sinusitis',
    54: 'substance-related mental disorder',
    55: 'postpartum depression',
    56: 'coronary atherosclerosis',
    57: 'spondylitis',
    58: 'pituitary adenoma',
    59: 'uterine fibroids',
    60: 'idiopathic nonmenstrual bleeding',
    61: 'chalazion',
    62: 'ovarian torsion',
    63: 'retinopathy due to high blood pressure',
    64: 'vaginal yeast infection',
    65: 'mastoiditis',
    66: 'lung contusion',
    67: 'hypertrophic obstructive cardiomyopathy (hocm)',
    68: 'ingrown toe nail',
    69: 'pulmonary eosinophilia',
    70: 'corneal disorder',
    71: 'foreign body in the gastrointestinal tract',
    72: 'endophthalmitis',
    73: 'intestinal malabsorption',
    74: 'viral warts',
    75: 'hyperhidrosis',
    76: 'stroke',
    77: 'pilonidal cyst',
    78: 'crushing injury',
    79: 'normal pressure hydrocephalus',
    80: 'alopecia',
    81: 'hashimoto thyroiditis',
    82: 'flat feet',
    83: 'nonalcoholic liver disease (nash)',
    84: 'hemarthrosis',
    85: 'pelvic organ prolapse',
    86: 'fracture of the arm',
    87: 'coagulation (bleeding) disorder',
    88: 'intracranial hemorrhage',
    89: 'hyperkalemia',
    90: 'cornea infection',
    91: 'abscess of the lung',
    92: 'dengue fever',
    93: 'chronic sinusitis',
    94: 'cholesteatoma',
    95: 'volvulus',
    96: 'injury to the finger',
    97: 'poisoning due to analgesics',
    98: 'atrial fibrillation',
    99: 'pinworm infection',
    100: 'urethral valves',
    101: 'open wound of the neck',
    102: 'achalasia',
    103: 'conductive hearing loss',
    104: 'abdominal hernia',
    105: 'cerebral palsy',
    106: 'marijuana abuse',
    107: 'cryptococcosis',
    108: 'obesity',
    109: 'indigestion',
    110: 'bursitis',
    111: 'esophageal cancer',
    112: 'pulmonary congestion',
    113: 'juvenile rheumatoid arthritis',
    114: 'actinic keratosis',
    115: 'acute otitis media',
    116: 'astigmatism',
    117: 'tuberous sclerosis',
    118: 'empyema',
    119: 'presbyacusis',
    120: 'neonatal jaundice',
    121: 'chronic obstructive pulmonary disease (copd)',
    122: 'dislocation of the elbow',
    123: 'spondylosis',
    124: 'herpangina',
    125: 'injury to the shoulder',
    126: 'poisoning due to antidepressants',
    127: 'infection of open wound',
    128: 'deep vein thrombosis (dvt)',
    129: 'protein deficiency',
    130: 'myoclonus',
    131: 'bone spur of the calcaneous',
    132: 'von willebrand disease',
    133: 'open wound of the back',
    134: 'heart block',
    135: 'colonic polyp',
    136: 'magnesium deficiency',
    137: 'female infertility of unknown cause',
    138: 'pericarditis',
    139: 'attention deficit hyperactivity disorder (adhd)',
    140: 'pulmonic valve disease',
    141: 'tietze syndrome',
    142: 'cranial nerve palsy',
    143: 'injury to the arm',
    144: 'conversion disorder',
    145: 'complex regional pain syndrome',
    146: 'otosclerosis',
    147: 'injury to the trunk',
    148: 'hypothyroidism',
    149: 'primary insomnia',
    150: 'lice',
    151: 'vitamin b12 deficiency',
    152: 'diabetes',
    153: 'vulvodynia',
    154: 'endometriosis',
    155: 'vasculitis',
    156: 'concussion',
    157: 'oral leukoplakia',
    158: 'chronic kidney disease',
    159: 'bladder disorder',
    160: 'chorioretinitis',
    161: 'priapism',
    162: 'myositis',
    163: 'mononucleosis',
    164: 'neuralgia',
    165: 'polycystic kidney disease',
    166: 'bipolar disorder',
    167: 'amyloidosis',
    168: 'chronic inflammatory demyelinating polyneuropathy (cidp)',
    169: 'gastroesophageal reflux disease (gerd)',
    170: 'vitreous hemorrhage',
    171: 'poisoning due to antimicrobial drugs',
    172: 'open wound of the mouth',
    173: 'scleroderma',
    174: 'myasthenia gravis',
    175: 'hypoglycemia',
    176: 'idiopathic absence of menstruation',
    177: 'dislocation of the ankle',
    178: 'carbon monoxide poisoning',
    179: 'panic attack',
    180: 'plantar fasciitis',
    181: 'hyperopia',
    182: 'poisoning due to sedatives',
    183: 'pemphigus',
    184: 'peyronie disease',
    185: 'hiatal hernia',
    186: 'extrapyramidal effect of drugs',
    187: 'meniere disease',
    188: 'anal fissure',
    189: 'allergy',
    190: 'chronic otitis media',
    191: 'fracture of the finger',
    192: 'hirschsprung disease',
    193: 'polymyalgia rheumatica',
    194: 'lymphedema',
    195: 'bladder cancer',
    196: 'acute bronchospasm',
    197: 'acute glaucoma',
    198: 'open wound of the chest',
    199: 'dislocation of the patella',
    200: 'sciatica',
    201: 'hypercalcemia',
    202: 'stress incontinence',
    203: 'varicose veins',
    204: 'benign kidney cyst',
    205: 'hydrocele of the testicle',
    206: 'degenerative disc disease',
    207: 'hirsutism',
    208: 'dislocation of the foot',
    209: 'hydronephrosis',
    210: 'diverticulosis',
    211: 'pain after an operation',
    212: 'huntington disease',
    213: 'lymphoma',
    214: 'dermatitis due to sun exposure',
    215: 'anemia due to chronic kidney disease',
    216: 'injury to internal organ',
    217: 'scleritis',
    218: 'pterygium',
    219: 'fungal infection of the skin',
    220: 'insulin overdose',
    221: 'syndrome of inappropriate secretion of adh (siadh)',
    222: 'foreign body in the ear',
    223: 'premenstrual tension syndrome',
    224: 'orbital cellulitis',
    225: 'injury to the leg',
    226: 'hepatic encephalopathy',
    227: 'bone cancer',
    228: 'syringomyelia',
    229: 'pulmonary fibrosis',
    230: 'mitral valve disease',
    231: 'parkinson disease',
    232: 'gout',
    233: 'otitis media',
    234: 'drug abuse (opioids)',
    235: 'myelodysplastic syndrome',
    236: 'fracture of the shoulder',
    237: 'acute kidney injury',
    238: 'threatened pregnancy',
    239: 'intracranial abscess',
    240: 'gum disease',
    241: 'open wound from surgical incision',
    242: 'gastrointestinal hemorrhage',
    243: 'seborrheic dermatitis',
    244: 'drug abuse (methamphetamine)',
    245: 'torticollis',
    246: 'poisoning due to antihypertensives',
    247: 'tension headache',
    248: 'alcohol intoxication',
    249: 'scurvy',
    250: 'narcolepsy',
    251: 'food allergy',
    252: 'labyrinthitis',
    253: 'anxiety',
    254: 'impulse control disorder',
    255: 'stenosis of the tear duct',
    256: 'abscess of nose',
    257: 'omphalitis',
    258: 'leukemia',
    259: 'bell palsy',
    260: 'conjunctivitis due to allergy',
    261: 'drug reaction',
    262: 'adrenal cancer',
    263: 'myopia',
    264: 'osteoarthritis',
    265: 'thyroid disease',
    266: 'pharyngitis',
    267: 'chronic rheumatic fever',
    268: 'hypocalcemia',
    269: 'macular degeneration',
    270: 'pneumonia',
    271: 'cold sore',
    272: 'premature ventricular contractions (pvcs)',
    273: 'testicular cancer',
    274: 'hydrocephalus',
    275: 'breast cancer',
    276: 'anemia due to malignancy',
    277: 'esophageal varices',
    278: 'endometrial cancer',
    279: 'cystic fibrosis',
    280: 'intertrigo (skin condition)',
    281: 'parathyroid adenoma',
    282: 'glucocorticoid deficiency',
    283: 'temporomandibular joint disorder',
    284: 'wilson disease',
    285: 'vesicoureteral reflux',
    286: 'vitamin a deficiency',
    287: 'gonorrhea',
    288: 'fracture of the rib',
    289: 'ependymoma',
    290: 'hepatitis due to a toxin',
    291: 'vaginal cyst',
    292: 'open wound of the shoulder',
    293: 'ectopic pregnancy',
    294: 'chronic knee pain',
    295: 'pinguecula',
    296: 'hypergammaglobulinemia',
    297: 'alcohol abuse',
    298: 'carpal tunnel syndrome',
    299: 'pituitary disorder',
    300: 'kidney stone',
    301: 'autism',
    302: 'cat scratch disease',
    303: 'chronic glaucoma',
    304: 'retinal detachment',
    305: 'aplastic anemia',
    306: 'overflow incontinence',
    307: 'hemolytic anemia',
    308: 'lateral epicondylitis (tennis elbow)',
    309: 'open wound of the eye',
    310: 'syphilis',
    311: 'diabetic kidney disease',
    312: 'nose disorder',
    313: 'drug withdrawal',
    314: 'dental caries',
    315: 'hypercholesterolemia',
    316: 'fracture of the patella',
    317: 'kidney failure',
    318: 'fracture of the neck',
    319: 'muscle spasm',
    320: 'hemophilia',
    321: 'hyperosmotic hyperketotic state',
    322: 'peritonsillar abscess',
    323: 'gastroparesis',
    324: 'itching of unknown cause',
    325: 'polycythemia vera',
    326: 'thrombocytopenia',
    327: 'head and neck cancer',
    328: 'pseudohypoparathyroidism',
    329: 'goiter',
    330: 'urge incontinence',
    331: 'edward syndrome',
    332: 'open wound of the arm',
    333: 'muscular dystrophy',
    334: 'mittelschmerz',
    335: 'corneal abrasion',
    336: 'anemia of chronic disease',
    337: 'dysthymic disorder',
    338: 'scarlet fever',
    339: 'hypertensive heart disease',
    340: 'drug abuse (barbiturates)',
    341: 'polycystic ovarian syndrome (pcos)',
    342: 'encephalitis',
    343: 'cyst of the eyelid',
    344: 'balanitis',
    345: 'foreign body in the throat',
    346: 'drug abuse (cocaine)',
    347: 'optic neuritis',
    348: 'alcohol withdrawal',
    349: 'premature atrial contractions (pacs)',
    350: 'hemiplegia',
    351: 'hammer toe',
    352: 'open wound of the cheek',
    353: 'joint effusion',
    354: 'open wound of the knee',
    355: 'meningioma',
    356: 'brain cancer',
    357: 'placental abruption',
    358: 'seasonal allergies (hay fever)',
    359: 'lung cancer',
    360: 'primary kidney disease',
    361: 'uterine cancer',
    362: 'dry eye of unknown cause',
    363: 'fibrocystic breast disease',
    364: 'fungal infection of the hair',
    365: 'tooth abscess',
    366: 'envenomation from spider or animal bite',
    367: 'vacterl syndrome',
    368: 'vertebrobasilar insufficiency',
    369: 'rectal disorder',
    370: 'atonic bladder',
    371: 'benign paroxysmal positional vertical (bppv)',
    372: 'blepharospasm',
    373: 'sarcoidosis',
    374: 'metastatic cancer',
    375: 'trigger finger (finger disorder)',
    376: 'stye',
    377: 'hemochromatosis',
    378: 'osteochondroma',
    379: 'cushing syndrome',
    380: 'typhoid fever',
    381: 'vitreous degeneration',
    382: 'atrophic',
    383: 'aspergillosis',
    384: 'uterine atony',
    385: 'trichinosis',
    386: 'whooping cough',
    387: 'open wound of the lip',
    388: 'subacute thyroiditis',
    389: 'oral mucosal lesion',
    390: 'open wound due to trauma',
    391: 'intracerebral hemorrhage',
    392: 'alzheimer disease',
    393: 'vaginismus',
    393: 'vaginismus',
    394: 'systemic lupus erythematosis (sle)',
    395: 'premature ovarian failure',
    396: 'thoracic outlet syndrome',
    397: 'ganglion cyst',
    398: 'dislocation of the knee',
    399: 'crohn disease',
    400: 'postoperative infection',
    401: 'folate deficiency',
    402: 'fluid overload',
    403: 'atrial flutter',
    404: 'skin disorder',
    405: 'floaters',
    406: 'tooth disorder',
    407: 'heart attack',
    408: 'open wound of the abdomen',
    409: 'fracture of the leg',
    410: 'oral thrush (yeast infection)',
    411: 'pityriasis rosea',
    412: 'allergy to animals',
    413: 'orthostatic hypotension',
    414: 'obstructive sleep apnea (osa)',
    415: 'hypokalemia',
    416: 'psoriasis',
    417: 'dislocation of the shoulder',
    418: 'intussusception',
    419: 'cervicitis',
    420: 'abscess of the pharynx',
    421: 'primary thrombocythemia',
    422: 'arthritis of the hip',
    423: 'decubitus ulcer',
    424: 'hypernatremia',
    425: 'sensorineural hearing loss',
    426: 'chronic ulcer',
    427: 'osteoporosis',
    428: 'ileus',
    429: 'sickle cell crisis',
    430: 'urethritis',
    431: 'prostatitis',
    432: 'otitis externa (swimmer\'s ear)',
    433: 'poisoning due to anticonvulsants',
    434: 'testicular torsion',
    435: 'tricuspid valve disease',
    436: 'urethral stricture',
    437: 'vitamin d deficiency',
    438: 'hydatidiform mole',
    439: 'pain disorder affecting the neck',
    440: 'tuberculosis',
    441: 'pelvic fistula',
    442: 'acute bronchiolitis',
    443: 'presbyopia',
    444: 'dementia',
    445: 'insect bite',
    446: 'paroxysmal ventricular tachycardia',
    447: 'congenital heart defect',
    448: 'connective tissue disorder',
    449: 'foreign body in the eye',
    450: 'poisoning due to gas',
    451: 'pyogenic skin infection',
    452: 'endometrial hyperplasia',
    453: 'acanthosis nigricans',
    454: 'central atherosclerosis',
    455: 'viral exanthem',
    456: 'noninfectious gastroenteritis',
    457: 'benign prostatic hyperplasia (bph)',
    458: 'menopause',
    459: 'primary immunodeficiency',
    460: 'ovarian cancer',
    461: 'cataract',
    462: 'dislocation of the hip',
    463: 'spinal stenosis',
    464: 'intestinal obstruction',
    465: 'heart contusion',
    466: 'congenital malformation syndrome',
    467: 'sporotrichosis',
    468: 'lymphangitis',
    469: 'wernicke korsakoff syndrome',
    470: 'intestinal disease',
    471: 'acute bronchitis',
    472: 'persistent vomiting of unknown cause',
    473: 'open wound of the foot',
    474: 'myocarditis',
    475: 'preeclampsia',
    476: 'ischemic heart disease',
    477: 'neurofibromatosis',
    478: 'chickenpox',
    479: 'pancreatic cancer',
    480: 'neuropathy due to drugs',
    481: 'croup',
    482: 'idiopathic excessive menstruation',
    483: 'amblyopia',
    484: 'meckel diverticulum',
    485: 'dislocation of the wrist',
    486: 'ear drum damage',
    487: 'erectile dysfunction',
    488: 'temporary or benign blood in urine',
    489: 'kidney disease due to longstanding hypertension',
    490: 'chondromalacia of the patella',
    491: 'onychomycosis',
    492: 'urethral disorder',
    493: 'lyme disease',
    494: 'iron deficiency anemia',
    495: 'acute respiratory distress syndrome (ards)',
    496: 'toxic multinodular goiter',
    497: 'open wound of the finger',
    498: 'autonomic nervous system disorder',
    499: 'psychosexual disorder',
    500: 'anemia',
    501: 'tendinitis',
    502: 'common cold',
    503: 'amyotrophic lateral sclerosis (als)',
    504: 'central retinal artery or vein occlusion',
    505: 'paroxysmal supraventricular tachycardia',
    506: 'venous insufficiency',
    507: 'trichomonas infection',
    508: 'acne',
    509: 'depression',
    510: 'drug abuse',
    511: 'urinary tract obstruction',
    512: 'diabetes insipidus',
    513: 'iridocyclitis',
    514: 'varicocele of the testicles',
    515: 'irritable bowel syndrome',
    516: 'fracture of the foot',
    517: 'ovarian cyst',
    518: 'chlamydia',
    519: 'parasitic disease',
    520: 'fracture of the jaw',
    521: 'lipoma',
    522: 'female genitalia infection',
    523: 'pulmonary hypertension',
    524: 'thyroid nodule',
    525: 'broken tooth',
    526: 'dumping syndrome',
    527: 'lymphadenitis',
    528: 'injury to the face',
    529: 'aortic valve disease',
    530: 'rheumatoid arthritis',
    531: 'spermatocele',
    532: 'impetigo',
    533: 'anal fistula',
    534: 'hypothermia',
    535: 'oppositional disorder',
    536: 'migraine',
    537: 'diabetic peripheral neuropathy',
    538: 'testicular disorder',
    539: 'gestational diabetes',
    540: 'hidradenitis suppurativa',
    541: 'valley fever',
    542: 'conjunctivitis due to bacteria',
    543: 'lewy body dementia',
    544: 'multiple myeloma',
    545: 'head injury',
    546: 'ascending cholangitis',
    547: 'idiopathic irregular menstrual cycle',
    548: 'interstitial lung disease',
    549: 'mononeuritis',
    550: 'malaria',
    551: 'somatization disorder',
    552: 'hypovolemia',
    553: 'schizophrenia',
    554: 'knee ligament or meniscus tear',
    555: 'endocarditis',
    556: 'sepsis',
    557: 'heat stroke',
    558: 'cholecystitis',
    559: 'cardiac arrest',
    560: 'cardiomyopathy',
    561: 'social phobia',
    562: 'meningitis',
    563: 'spherocytosis',
    564: 'hormone disorder',
    565: 'raynaud disease',
    566: 'reactive arthritis',
    567: 'scabies',
    568: 'ear wax impaction',
    569: 'hypertension of pregnancy',
    570: 'peripheral arterial embolism',
    571: 'rosacea',
    572: 'fracture of the skull',
    573: 'uveitis',
    574: 'fracture of the facial bones',
    575: 'tracheitis',
    576: 'jaw disorder',
    577: 'perirectal infection',
    578: 'breast cyst',
    579: 'post-traumatic stress disorder (ptsd)',
    580: 'kidney cancer',
    581: 'vulvar cancer',
    582: 'blepharitis',
    583: 'celiac disease',
    584: 'cystitis',
    585: 'sickle cell anemia',
    586: 'subconjunctival hemorrhage',
    587: 'hemorrhoids',
    588: 'contact dermatitis',
    589: 'sinus bradycardia',
    590: 'high blood pressure',
    591: 'pelvic inflammatory disease',
    592: 'liver disease',
    593: 'chronic constipation',
    594: 'thyroid cancer',
    595: 'flu',
    596: 'friedrich ataxia',
    597: 'tic (movement) disorder',
    598: 'skin polyp',
    599: 'brachial neuritis',
    600: 'cervical cancer',
    601: 'adrenal adenoma',
    602: 'esophagitis',
    603: 'gas gangrene',
    604: 'yeast infection',
    605: 'spina bifida',
    606: 'drug poisoning due to medication',
    607: 'alcoholic liver disease',
    608: 'malignant hypertension',
    609: 'diverticulitis',
    610: 'moyamoya disease',
    611: 'heat exhaustion',
    612: 'psychotic disorder',
    613: 'frostbite',
    614: 'atrophy of the corpus cavernosum',
    615: 'smoking or tobacco addiction',
    616: 'sprain or strain',
    617: 'essential tremor',
    618: 'open wound of the ear',
    619: 'foreign body in the nose',
    620: 'idiopathic painful menstruation',
    621: 'down syndrome',
    622: 'idiopathic infrequent menstruation',
    623: 'pneumothorax',
    624: 'de quervain disease',
    625: 'fracture of the vertebra',
    626: 'human immunodeficiency virus infection (hiv)',
    627: 'mumps',
    628: 'subarachnoid hemorrhage',
    629: 'acute fatty liver of pregnancy (aflp)',
    630: 'ectropion',
    631: 'scar',
    632: 'lactose intolerance',
    633: 'eustachian tube dysfunction (ear disorder)',
    634: 'appendicitis',
    635: 'graves disease',
    636: 'dissociative disorder',
    637: 'open wound of the face',
    638: 'dislocation of the vertebra',
    639: 'phimosis',
    640: 'hyperemesis gravidarum',
    641: 'pregnancy',
    642: 'thalassemia',
    643: 'placenta previa',
    644: 'epidural hemorrhage',
    645: 'septic arthritis',
    646: 'athlete\'s foot',
    647: 'pleural effusion',
    648: 'aphakia',
    649: 'vulvar disorder',
    650: 'sialoadenitis',
    651: 'gynecomastia',
    652: 'urinary tract infection',
    653: 'histoplasmosis',
    654: 'erythema multiforme',
    655: 'scoliosis',
    656: 'bunion',
    657: 'arrhythmia',
    658: 'trigeminal neuralgia',
    659: 'ankylosing spondylitis',
    660: 'peripheral nerve disorder',
    661: 'sebaceous cyst',
    662: 'poisoning due to antipsychotics',
    663: 'neurosis',
    664: 'prostate cancer',
    665: 'cerebral edema',
    666: 'dislocation of the finger',
    667: 'birth trauma',
    668: 'chronic pancreatitis',
    669: 'hematoma',
    670: 'carcinoid syndrome',
    671: 'open wound of the head',
    672: 'seborrheic keratosis',
    673: 'burn',
    674: 'spontaneous abortion',
    675: 'genital herpes',
    676: 'adjustment reaction',
    677: 'gallstone',
    678: 'multiple sclerosis',
    679: 'zenker diverticulum',
    680: 'fracture of the pelvis',
    681: 'pneumoconiosis',
    682: 'hyperlipidemia',
    683: 'ulcerative colitis',
    684: 'male genitalia infection',
    685: 'hpv',
    686: 'angina',
    687: 'injury to the spinal cord',
    688: 'nasal polyp',
    689: 'lichen simplex',
    690: 'trichiasis',
    691: 'acariasis',
    692: 'colorectal cancer',
    693: 'skin pigmentation disorder',
    694: 'factitious disorder',
    695: 'lymphogranuloma venereum',
    696: 'galactorrhea of unknown cause',
    697: 'g6pd enzyme deficiency',
    698: 'nerve impingement near the shoulder',
    699: 'toxoplasmosis',
    700: 'fibroadenoma',
    701: 'open wound of the hand',
    702: 'missed abortion',
    703: 'diabetic ketoacidosis',
    704: 'granuloma inguinale',
    705: 'obsessive compulsive disorder (ocd)',
    706: 'injury of the ankle',
    707: 'hyponatremia',
    708: 'stricture of the esophagus',
    709: 'fracture of the ankle',
    710: 'soft tissue sarcoma',
    711: 'bone disorder',
    712: 'epilepsy',
    713: 'personality disorder',
    714: 'shingles (herpes zoster)',
    715: 'tourette syndrome',
    716: 'avascular necrosis',
    717: 'strep throat',
    718: 'spinocerebellar ataxia',
    719: 'osteomyelitis',
    720: 'sjögren syndrome',
    721: 'adhesive capsulitis of the shoulder',
    722: 'viral hepatitis',
    723: 'tonsillar hypertrophy',
    724: 'gastritis',
    725: 'skin cancer',
    726: 'rheumatic fever',
    727: 'aphthous ulcer',
    728: 'tonsillitis',
    729: 'intestinal cancer',
    730: 'rocky mountain spotted fever',
    731: 'stomach cancer',
    732: 'developmental disability',
    733: 'acute stress reaction',
    734: 'delirium',
    735: 'callus',
    736: 'guillain barre syndrome',
    737: 'lumbago',
    738: 'deviated nasal septum',
    739: 'hemangioma',
    740: 'peripheral arterial disease',
    741: 'chronic back pain',
    742: 'heart failure',
    743: 'conjunctivitis',
    744: 'herniated disk',
    745: 'rhabdomyolysis',
    746: 'breast infection (mastitis)',
    747: 'abdominal aortic aneurysm',
    748: 'pulmonary embolism',
    749: 'conduct disorder',
    750: 'mastectomy',
    751: 'epididymitis',
    752: 'premature rupture of amniotic membrane',
    753: 'molluscum contagiosum',
    754: 'necrotizing fasciitis',
    755: 'benign vaginal discharge (leukorrhea)',
    756: 'bladder obstruction',
    757: 'melanoma',
    758: 'cervical disorder',
    759: 'laryngitis',
    760: 'dyshidrosis',
    761: 'poisoning due to opioids',
    762: 'diaper rash',
    763: 'lichen planus',
    764: 'gastroduodenal ulcer',
    765: 'inguinal hernia',
    766: 'eczema',
    767: 'asperger syndrome',
    768: 'mucositis',
    769: 'paronychia',
    770: 'open wound of the jaw',
    771: 'white blood cell disease',
    772: 'kaposi sarcoma',
    773: 'spondylolisthesis',
    774: 'pseudotumor cerebri',
    775: 'conjunctivitis due to virus',
    776: 'open wound of the nose'   
}

df['illness'] = df['predicted_risk'].map(risk_to_illness)

print("Predictions from Database:")
print(df)

csv_data = pd.read_csv("dands.csv")

csv_data = csv_data[["headache", "dizziness", "fatigue", "fever"]]

csv_predictions = model.predict(csv_data)

csv_data['predicted_risk'] = csv_predictions

csv_data['illness'] = csv_data['predicted_risk'].map(risk_to_illness)

print("Predictions from CSV:")
print(csv_data)