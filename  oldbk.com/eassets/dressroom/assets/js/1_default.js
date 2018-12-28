var basePath = '/eassets/dressroom/images/';
var _default = {
    'own' : {
        'strange'   : 3,
        'agility'   : 3,
        'intuition' : 3,
        'endurance' : 3
    },
    'name' : {
        'items': [
            'earrings','tshort','cloak','necklace','weapons',
            'armor','ring1','ring2','ring3','helmet','glove',
            'shield','shoes','rune1','rune2','rune3'
        ],
        'stats': ['strange','agility','intuition','endurance','intellect','wisdom'],
        'mf': ['critical','p_critical','flee','p_flee'],
        'possession': ['knife','ax','sword','baton'],
        'm_possession': ['fire','water','earth','air','grey','light','dark'],
        'rune_possession': ['fire','water','earth','air'],
        'armor': ['head','body','belt','feet'],
        'increased': ['damage','armor','mf'],
        'class': {
            1: 'Уворот',
            2: 'Крит',
            3: 'Танк'
        }
    },
    'shop' : {
        'fair' : {
            'exclude': ['tshort','cloak']
        }
    },
    'level' : [
        //0ой
        {
            'default':{'stat':15,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':3},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //1ый
        {
            'default':{'stat':4,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':4},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //2ой
        {
            'default':{'stat':4,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':5},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //3ий
        {
            'default':{'stat':4,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':6},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //4ый
        {
            'default':{'stat':6,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':7},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //5ый
        {
            'default':{'stat':4,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':8},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //6ой
        {
            'default':{'stat':4,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':9},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //7ой
        {
            'default':{'stat':6,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':10},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //8ой
        {
            'default':{'stat':6,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':11},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //9ый
        {
            'default':{'stat':9,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':13},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //10ый
        {
            'default':{'stat':12,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':16},
            'up':[
                {'stat':0,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0}
            ]
        },
        //11ый
        {
            'default':{'stat':13,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':19},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0}
            ]
        },
        //12ый
        {
            'default':{'stat':14,'possession':2},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':23},
            'up':[
                {'stat':0,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':3,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':5,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0}
            ]
        },
        //13ый
        {
            'default':{'stat':15,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':28},
            'up':[
                {'stat':0,'possession':0},
                {'stat':1,'possession':0},
                {'stat':1,'possession':0},
                {'stat':5,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':5,'possession':0},
                {'stat':5,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0},
                {'stat':2,'possession':0}
            ]
        },
        //14ый
        {
            'default':{'stat':20,'possession':1},
            'min': {'strange':3,'agility':3,'intuition':3,'endurance':40},
            'up':[
                {'stat':0,'possession':0}
            ]
        }
    ],
    'podgon' : {
        'info': [
            {'mf': 0, 'cost': 0},
            {'mf': 2, 'cost': 0.2},
            {'mf': 3, 'cost': 0.2},
            {'mf': 4, 'cost': 0.4},
            {'mf': 6, 'cost': 0.7},
            {'mf': 10, 'cost': 0.1}
        ],
        'can' : ['earrings','necklace','armor','ring','helmet','glove','shield','shoes']
    },
    'prokat': {
        'can': ['earrings','necklace','armor','ring','helmet','glove','shield','shoes']
    },
    'sharpen' : {
        'info': {
            'knife' : {'cost': 6,'give': {'min_damage':1,'max_damage':1},'need': {'intuition':1,'knife':1}},
            'sword' : {'cost': 6,'give': {'min_damage':1,'max_damage':1},'need': {'endurance':1,'sword':1}},
            'ax' : {'cost': 6,'give': {'min_damage':1,'max_damage':1},'need': {'strange':1,'ax':1}},
            'baton' : {'cost': 6,'give': {'min_damage':1,'max_damage':1},'need': {'agility':1,'baton':1}}
        },
        'can' : ['knife','ax','sword','baton']
    },
    'mf' : {
        'can' : ['earrings','necklace','armor','ring','helmet','glove','shield','shoes', 'tshort', 'cloak']
    },
    'rune' : [
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':0, 'armor':0, 'm_possession':0}, //0
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':10, 'armor':1, 'm_possession':0}, //1
        {'property':{'hp':5, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //2
        {'property':{'hp':0, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':10, 'armor':1, 'm_possession':0}, //3
        {'property':{'hp':5, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':0, 'armor':0, 'm_possession':0}, //4
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':10, 'armor':1, 'm_possession':0}, //5
        {'property':{'hp':5, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //6
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':10, 'armor':1, 'm_possession':0}, //7
        {'property':{'hp':5, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //8
        {'property':{'hp':0, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':10, 'armor':1, 'm_possession':0}, //9
        {'property':{'hp':10, 'wisdom':0,'intellect':0,'min_damage':1,'max_damage':2}, 'stat':2, 'mf':0, 'armor':0, 'm_possession':1}, //10
        {'property':{'hp':0, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':15, 'armor':1, 'm_possession':0}, //11
        {'property':{'hp':7, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //12
        {'property':{'hp':0, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':15, 'armor':1, 'm_possession':0}, //13
        {'property':{'hp':7, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //14
        {'property':{'hp':0, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':15, 'armor':1, 'm_possession':1}, //15
        {'property':{'hp':7, 'wisdom':0,'intellect':0,'min_damage':1,'max_damage':2}, 'stat':1, 'mf':0, 'armor':1, 'm_possession':0}, //16
        {'property':{'hp':0, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':15, 'armor':1, 'm_possession':0}, //17
        {'property':{'hp':7, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':0, 'armor':0, 'm_possession':0}, //18
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':0, 'mf':15, 'armor':1, 'm_possession':0}, //19
        {'property':{'hp':15, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':2, 'mf':20, 'armor':0, 'm_possession':1}, //20
        {'property':{'hp':16, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':2, 'mf':15, 'armor':0, 'm_possession':0}, //21
        {'property':{'hp':0, 'wisdom':1,'intellect':0,'min_damage':1,'max_damage':1}, 'stat':1, 'mf':16, 'armor':0, 'm_possession':0}, //22
        {'property':{'hp':0, 'wisdom':1,'intellect':1,'min_damage':1,'max_damage':1}, 'stat':0, 'mf':17, 'armor':0, 'm_possession':0}, //23
        {'property':{'hp':17, 'wisdom':0,'intellect':0,'min_damage':1,'max_damage':1}, 'stat':2, 'mf':18, 'armor':0, 'm_possession':1}, //24
        {'property':{'hp':0, 'wisdom':0,'intellect':0,'min_damage':1,'max_damage':2}, 'stat':2, 'mf':25, 'armor':2, 'm_possession':0}, //25
        {'property':{'hp':18, 'wisdom':0,'intellect':1,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':20, 'armor':0, 'm_possession':0}, //26
        {'property':{'hp':0, 'wisdom':1,'intellect':2,'min_damage':0,'max_damage':0}, 'stat':2, 'mf':21, 'armor':0, 'm_possession':0}, //27
        {'property':{'hp':19, 'wisdom':0,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':2, 'mf':22, 'armor':0, 'm_possession':1}, //28
        {'property':{'hp':20, 'wisdom':1,'intellect':0,'min_damage':0,'max_damage':0}, 'stat':1, 'mf':35, 'armor':0, 'm_possession':0}, //29
        {'property':{'hp':30, 'wisdom':0,'intellect':0,'min_damage':1,'max_damage':2}, 'stat':3, 'mf':30, 'armor':3, 'm_possession':1} //30
    ],
    'unik': {
        'u': [
            {'min':0,'max':5,'value':0,'text':'Уникальный бронзовый бонус','next':6},
            {'min':6,'max':8,'value':1,'text':'Уникальный бронзовый бонус','next':9},
            {'min':9,'max':11,'value':2,'text':'Уникальный серебрянный бонус','next':12},
            {'min':12,'max':12,'value':3, 'text': 'Уникальный золотой бонус','next':13},
            {'min':13,'max':100,'value':4, 'text':' Уникальный платиновый бонус','next':13}
        ],
        'uu': [
            {'min':0,'max':5,'value':0, 'text': 'Легендарный бронзовый бонус','next':6},
            {'min':6,'max':8,'value':2, 'text': 'Легендарный бронзовый бонус','next':9},
            {'min':9,'max':11,'value':4, 'text': 'Легендарный серебрянный бонус','next':12},
            {'min':12,'max':12,'value':6, 'text': 'Легендарный золотой бонус','next':13},
            {'min':13,'max':100,'value':8, 'text': 'Легендарный платиновый бонус','next':13}
        ]
    },
    'charka': {
        'can': ['earrings','necklace','armor','ring','helmet','glove','shield','shoes'],
        'info': [
            {'static': {'hp': 0}, 'dynamic': {'stats':0,'mf':0}, 'min_level': 0},
            {'static': {'hp': 5}, 'dynamic': {'stats':1,'mf':10}, 'min_level': 6},
            {'static': {'hp': 15}, 'dynamic': {'stats':2,'mf':20,'possession':1,'m_possession':0}, 'min_level': 6},
            {'static': {'hp': 25}, 'dynamic': {'stats':3,'mf':30,'possession':0,'m_possession':1}, 'min_level': 6},
            {'static': {'hp': 35}, 'dynamic': {'stats':4,'mf':40,'possession':1,'m_possession':1}, 'min_level': 10},
        ]
    },
    'art' : {
        'can': ['earrings','necklace','weapons', 'armor','helmet','glove', 'shield','shoes']
    },
    'art_usil': [
        [],
        [
            {},
            {'armor':3, 'hp':40}, //1
            {'armor':11}, //2
            {'hp':55} //3
        ], //1
        [
            {},
            {'critical':70}, //1
            {'p_critical':70}, //2
            {'flee':70}, //3
            {'p_flee':70} //4
        ], //2
        [
            {},
            {'strange':7}, //1
            {'agility':7}, //2
            {'intuition':7}, //3
            {'intellect':7}, //4
            {'wisdom':7} //5
        ], //3
        [
            {},
            {'knife':2}, //1
            {'ax':2}, //2
            {'baton':2}, //3
            {'sword':2} //4
        ], //4
        [
            {},
            {'fire':2}, //1
            {'water':2}, //2
            {'earth':2}, //3
            {'air':2} //4
        ], //5
        [
            {},
            {'increased':{'armor':6}}, //1
            {'increased':{'mf':3}}, //2
            {'increased':{'mf':1,'damage':1}} //3
        ] //6
    ],
    'art_level': [
        {},
        {
            'level':7,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':8,'min_damage':2,'max_damage':2},'free':{'stats':1,'mf':7},'durability':5,'armor':1}
        },
        {
            'level':8,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':10,'min_damage':3,'max_damage':3},'free':{'stats':1,'mf':10},'durability':10,'armor':1}
        },
        {
            'level':9,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':12,'min_damage':4,'max_damage':4},'free':{'stats':1,'mf':12},'durability':10,'armor':1}
        },
        {
            'level':10,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':15,'min_damage':1,'max_damage':1},'free':{'stats':1,'mf':15},'durability':15,'armor':1}
        },
        {
            'level':11,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':20,'min_damage':1,'max_damage':1},'free':{'stats':1,'mf':17},'durability':15,'armor':1}
        },
        {
            'level':12,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':27,'min_damage':2,'max_damage':2},'free':{'stats':1,'mf':22},'durability':15,'armor':1}
        },
        {
            'level':13,
            'need': {'strange':1,'agility':1,'intuition':1,'endurance':1,'knife':1,'ax':1,'sword':1,'baton':1},
            'give': {'property':{'hp':35,'min_damage':2,'max_damage':2},'free':{'stats':1,'mf':27},'durability':15,'armor':1}
        }
    ],
    'art_limit': {
      'stats': {
          1: ['intuition'],
          2: ['agility'],
          3: ['agility','intuition']
      },
      'mf': {
          1: ['critical'],
          2: ['flee'],
          3: ['critical','flee']
      }
    },
    'armor_eff': {
        7: {
           25: 30,
           30: 35,
           35: 40,
           40: 50,
           45: 60,
           50: 70,
           55: 80
        },
        8: {
            25: 30,
            30: 30,
            35: 35,
            40: 40,
            45: 45,
            50: 50,
            55: 55,
            60: 60,
            65: 65,
            70: 70,
            75: 75,
            80: 80
        },
        9: {
            25: 30,
            30: 30,
            35: 35,
            40: 40,
            45: 45,
            50: 50,
            60: 55,
            70: 60,
            80: 65,
            90: 70,
            100: 75,
            125: 80
        },
        10: {
            25: 30,
            30: 30,
            40: 35,
            50: 40,
            60: 45,
            70: 50,
            80: 55,
            95: 60,
            105: 65,
            115: 70,
            125: 75,
            150: 80
        },
        11: {
            25: 30,
            30: 30,
            40: 35,
            50: 40,
            60: 45,
            70: 50,
            80: 55,
            90: 60,
            110: 65,
            130: 70,
            150: 75,
            175: 80
        },
        12: {
            25: 30,
            30: 30,
            40: 35,
            60: 40,
            80: 45,
            100: 50,
            120: 55,
            130: 60,
            145: 65,
            160: 70,
            175: 75,
            200: 80
        },
        13: {
            25: 30,
            30: 30,
            40: 35,
            60: 40,
            80: 45,
            100: 50,
            120: 55,
            140: 60,
            160: 65,
            180: 70,
            200: 75,
            225: 80
        },
        14: {
            25: 30,
            30: 30,
            50: 35,
            75: 40,
            100: 45,
            125: 50,
            150: 55,
            175: 60,
            200: 65,
            225: 70,
            250: 75,
            275: 80
        }
    }
};

const STEP_BASE_ITEM = 'base-item';
const STEP_MOD_ITEM = 'mod-item';
const STEP_CREATE_ART = 'create-art';