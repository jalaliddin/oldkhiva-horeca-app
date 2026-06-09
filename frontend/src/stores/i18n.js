import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const languages = [
  { code: 'uz', label: "O'zbek", flag: '🇺🇿' },
  { code: 'en', label: 'English', flag: '🇬🇧' },
  { code: 'ru', label: 'Русский', flag: '🇷🇺' },
  { code: 'de', label: 'Deutsch', flag: '🇩🇪' },
]

const translations = {
  uz: {
    common: {
      logout: 'Chiqish', save: 'Saqlash', cancel: 'Bekor qilish', confirm: 'Tasdiqlash',
      back: 'Orqaga', next: 'Davom etish', close: 'Yopish', search: 'Qidirish',
      all: 'Barcha', status: 'Status', date: 'Sana', amount: 'Summa', total: 'Jami',
      balance: 'Qoldiq', paid: "To'langan", notes: 'Izoh', name: 'Nomi',
      price: 'Narxi', quantity: 'Miqdor', currency: "so'm", new: 'Yangi',
      pending: 'kutilmoqda', submit: 'Yuborish', view: "Ko'rish",
    },
    bookingStatus: {
      pending: 'Kutilmoqda', approved: 'Tasdiqlangan', rejected: 'Rad etilgan',
      cancelled: 'Bekor qilindi', completed: 'Tugallandi',
    },
    invoiceStatus: {
      unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: "Muddati o'tgan",
    },
    paymentMethod: {
      cash: 'Naqd', bank_transfer: "Bank o'tkazmasi", card: 'Karta',
    },
    nav: {
      home: 'Bosh sahifa', menu: 'Menyu', about: 'Haqimizda', contact: "Bog'lanish",
      login: 'Kirish', register: "Ro'yxatdan o'tish",
    },
    adminNav: {
      dashboard: 'Dashboard', clients: 'Mijozlar', contracts: 'Shartnomalar',
      menu: 'Menyu', services: 'Xizmatlar', bookings: 'Bronlar', invoices: 'Invoicelar',
      payments: "To'lovlar", reports: 'Hisobotlar', landingPage: 'Landing Page',
    },
    clientNav: {
      dashboard: 'Bosh sahifa', contract: 'Shartnoma', menu: 'Menyu',
      newBooking: 'Bron qilish', myBookings: 'Bronlarim', invoices: 'Invoicelar',
      payments: "To'lovlar",
    },
    login: {
      title: 'Tizimga kirish', email: 'Email', password: 'Parol', submit: 'Kirish',
      noAccount: "Hisobingiz yo'qmi?", register: "Ro'yhatdan o'ting",
      success: 'Muvaffaqiyatli kirdingiz!', error: 'Xato yuz berdi',
    },
    register: {
      title: "Ro'yhatdan o'tish", subtitle: "Turistik firma sifatida ro'yhatdan o'ting",
      step1: 'Asosiy', step2: 'Rekvizitlar', step3: 'Tasdiqlash',
      fullName: "To'liq ism", companyName: 'Kompaniya nomi', phone: 'Telefon',
      password: 'Parol', confirmPassword: 'Parolni tasdiqlang',
      directorName: 'Direktor ismi', inn: 'INN/STIR', bankName: 'Bank nomi',
      mfo: 'MFO', bankAccount: 'Hisob raqam', address: 'Manzil',
      company: 'Kompaniya', confirmData: "Barcha ma'lumotlar to'g'ri ekanligini tasdiqlayman",
      hasAccount: 'Allaqachon hisobingiz bormi?', login: 'Kirish',
      successMsg: "Arizangiz qabul qilindi! Admin tasdiqlashini kuting.",
      error: 'Xato yuz berdi',
    },
    dashboard: {
      title: 'Bosh sahifa', balance: 'Balans', totalBookings: 'Jami bronlar',
      pending: 'Kutilmoqda', unpaidInvoices: "To'lanmagan", recentBookings: "So'nggi bronlar",
      quickActions: 'Tezkor harakatlar', newBooking: 'Yangi bron',
      viewInvoices: 'Invoicelar', viewMenu: "Menyu ko'rish",
    },
    adminDashboard: {
      title: 'Dashboard', totalClients: 'Jami mijozlar', totalBookings: 'Jami bronlar',
      totalRevenue: 'Umumiy daromad', unpaidInvoices: "To'lanmagan invoice",
      recentBookings: "So'nggi bronlar", newApplications: 'Yangi arizalar',
      new: 'Yangi', noApplications: "Yangi arizalar yo'q", viewAll: 'Barchasi',
    },
    bookings: {
      title: 'Bronlar', myTitle: 'Bronlarim', bookingNum: 'Bron #', client: 'Mijoz',
      eventDate: 'Tadbir sanasi', guests: 'Mehmonlar', total: 'Jami summa',
      dateFrom: 'Sanadan', dateTo: 'Sanagacha', newBooking: 'Yangi bron',
    },
    bookingDetail: {
      title: 'Bron tafsiloti', eventDate: 'Tadbir sanasi',
      guestCount: 'Mehmonlar soni', guestSuffix: 'kishi', notes: 'Izoh',
      orderItems: 'Buyurtma tarkibi', adminAction: 'Admin amali', adminNotes: 'Admin izohi',
      approve: 'Tasdiqlash', reject: 'Rad etish',
      confirmApproveTitle: 'Bronni tasdiqlash',
      confirmApproveText: "Bu bronni tasdiqlaysizmi? Invoice avtomatik yaratiladi.",
      successApprove: 'Bron tasdiqlandi va invoice yaratildi!',
      successReject: 'Bron rad etildi.',
    },
    newBooking: {
      title: 'Yangi bron', step1: 'Asosiy', step2: 'Menyu', step3: 'Xizmatlar', step4: 'Tasdiqlash',
      eventDate: 'Tadbir sanasi', eventTime: 'Vaqt', guestCount: 'Mehmonlar soni',
      notesOpt: 'Izoh (ixtiyoriy)', quantity: 'Miqdor', noItems: 'Hech qanday mahsulot tanlanmagan',
      submitBooking: "Bron so'rovini yuborish",
      successMsg: "Bron so'rovi muvaffaqiyatli yuborildi!",
    },
    invoices: {
      title: 'Invoicelar', detail: 'Invoice tafsiloti', num: 'Invoice #',
      client: 'Mijoz', date: 'Sana', totalAmount: 'Jami summa', paidAmount: "To'langan",
      balance: 'Qoldiq', dueDate: 'Muddat', paymentStatus: "To'lov holati",
      subtotal: 'Subtotal', downloadPdf: 'PDF yuklab olish', booking: 'Bron',
      payDue: "To'lov muddati", orderItems: 'Buyurtma tarkibi',
      fileNotFound: 'Fayl topilmadi yoki hali yaratilmagan',
    },
    payments: {
      title: "To'lovlar", history: "To'lovlar tarixi", invoicePayments: "Invoice To'lovlar",
      deposits: 'Depozitlar', addTab: "+ To'lov kiritish", num: "To'lov #",
      date: 'Sana', type: 'Turi', method: "To'lov usuli", addNew: "Yangi to'lov kiritish",
      paymentType: "To'lov turi", client: 'Mijoz', amountLabel: "Summa (so'm)",
      paymentDate: "To'lov sanasi", bankRef: 'Bank referans (ixtiyoriy)',
      notesOpt: 'Izoh (ixtiyoriy)', savePayment: "To'lovni saqlash",
      lastUpdated: 'Oxirgi yangilanish', invoiceType: "Invoice to'lov", depositType: 'Depozit',
      successMsg: "To'lov saqlandi!",
    },
    clients: {
      title: 'Mijozlar', detail: 'Mijoz tafsiloti', company: 'Kompaniya',
      name: 'Ism', phone: 'Telefon', director: 'Direktor', inn: 'INN',
      bank: 'Bank', accountNum: 'Hisob raqam', address: 'Manzil', contract: 'Shartnoma',
      financialStatus: 'Moliyaviy holat', depositBalance: 'Depozit balansi',
      bookings: 'Bronlar', approve: 'Tasdiqlash', block: 'Bloklash',
      successApprove: 'Mijoz tasdiqlandi!', successBlock: 'Mijoz bloklandi!',
    },
    contract: {
      title: 'Shartnoma', signed: 'Shartnoma imzolangan',
      signedDesc: 'Siz shartnomani muvaffaqiyatli imzologansiz.',
      warningText: "Menyu va bron funksiyalaridan foydalanish uchun shartnomani imzolashingiz kerak.",
      download: 'Shartnomani yuklab olish', confirmTitle: 'Shartnomani tasdiqlash',
      agreeCheckbox: "Men shartnoma shartlarini o'qidim va roziman",
      noContract: "Hozircha faol shartnoma mavjud emas. Admin bilan bog'laning.",
      successMsg: 'Shartnoma muvaffaqiyatli imzolandi!',
      downloadError: 'Yuklab olishda xato yuz berdi',
    },
    menu: {
      title: 'Menyu', minOrder: 'Min buyurtma',
      label: 'BIZNING MENYU', sectionTitle: 'Taomlarimiz', currency: "so'm",
    },
    services: {
      title: 'Xizmatlar', newService: 'Yangi xizmat', edit: 'Tahrirlash',
      description: 'Tavsif', priceLabel: "Narxi (so'm)", unit: "O'lchov",
      active: 'Faol', inactive: 'Nofaol',
      added: "Xizmat qo'shildi!", updated: 'Xizmat yangilandi!', deleted: "Xizmat o'chirildi!",
    },
    reports: {
      title: 'Hisobotlar', clientsReport: 'Mijozlar hisoboti', invoicesReport: 'Invoice hisoboti',
      export: "Eksport qilish", generate: 'Hisobot olish',
    },
    landingSettings: { title: 'Landing Page Sozlamalari', save: 'Saqlash', preview: "Ko'rib chiqish" },
    menuManage: {
      title: 'Menyu Boshqaruvi', addCategory: "Kategoriya qo'shish", addItem: "Taom qo'shish",
      categoryName: 'Kategoriya nomi', edit: 'Tahrirlash', delete: "O'chirish",
    },
    hero: {
      location: "XIVA, O'ZBEKISTON", subtitle: "Xiva tarixining ta'mi",
      partner: "Hamkor bo'ling", viewMenu: "Menyu ko'rish",
    },
    features: { label: 'AFZALLIKLARIMIZ', title: 'Nima uchun OldKhiva?' },
    about: { label: 'HAQIMIZDA', cta: "Hamkor bo'lish" },
    cta: { title: "Hamkor bo'ling" },
    contact: {
      label: "BOG'LANISH", title: 'Kontakt', phone: 'Telefon',
      email: 'Email', address: 'Manzil',
    },
    footer: { rights: 'Barcha huquqlar himoyalangan.' },
  },

  en: {
    common: {
      logout: 'Logout', save: 'Save', cancel: 'Cancel', confirm: 'Confirm',
      back: 'Back', next: 'Continue', close: 'Close', search: 'Search',
      all: 'All', status: 'Status', date: 'Date', amount: 'Amount', total: 'Total',
      balance: 'Balance', paid: 'Paid', notes: 'Notes', name: 'Name',
      price: 'Price', quantity: 'Quantity', currency: 'UZS', new: 'New',
      pending: 'pending', submit: 'Submit', view: 'View',
    },
    bookingStatus: {
      pending: 'Pending', approved: 'Approved', rejected: 'Rejected',
      cancelled: 'Cancelled', completed: 'Completed',
    },
    invoiceStatus: {
      unpaid: 'Unpaid', partial: 'Partial', paid: 'Paid', overdue: 'Overdue',
    },
    paymentMethod: {
      cash: 'Cash', bank_transfer: 'Bank Transfer', card: 'Card',
    },
    nav: {
      home: 'Home', menu: 'Menu', about: 'About', contact: 'Contact',
      login: 'Login', register: 'Register',
    },
    adminNav: {
      dashboard: 'Dashboard', clients: 'Clients', contracts: 'Contracts',
      menu: 'Menu', services: 'Services', bookings: 'Bookings', invoices: 'Invoices',
      payments: 'Payments', reports: 'Reports', landingPage: 'Landing Page',
    },
    clientNav: {
      dashboard: 'Dashboard', contract: 'Contract', menu: 'Menu',
      newBooking: 'New Booking', myBookings: 'My Bookings', invoices: 'Invoices',
      payments: 'Payments',
    },
    login: {
      title: 'Sign In', email: 'Email', password: 'Password', submit: 'Sign In',
      noAccount: "Don't have an account?", register: 'Register',
      success: 'Logged in successfully!', error: 'An error occurred',
    },
    register: {
      title: 'Register', subtitle: 'Register as a travel agency',
      step1: 'Basic', step2: 'Details', step3: 'Confirm',
      fullName: 'Full Name', companyName: 'Company Name', phone: 'Phone',
      password: 'Password', confirmPassword: 'Confirm Password',
      directorName: 'Director Name', inn: 'Tax ID', bankName: 'Bank Name',
      mfo: 'MFO', bankAccount: 'Account Number', address: 'Address',
      company: 'Company', confirmData: 'I confirm all information is correct',
      hasAccount: 'Already have an account?', login: 'Sign In',
      successMsg: 'Application submitted! Awaiting admin approval.',
      error: 'An error occurred',
    },
    dashboard: {
      title: 'Dashboard', balance: 'Balance', totalBookings: 'Total Bookings',
      pending: 'Pending', unpaidInvoices: 'Unpaid', recentBookings: 'Recent Bookings',
      quickActions: 'Quick Actions', newBooking: 'New Booking',
      viewInvoices: 'Invoices', viewMenu: 'View Menu',
    },
    adminDashboard: {
      title: 'Dashboard', totalClients: 'Total Clients', totalBookings: 'Total Bookings',
      totalRevenue: 'Total Revenue', unpaidInvoices: 'Unpaid Invoices',
      recentBookings: 'Recent Bookings', newApplications: 'New Applications',
      new: 'New', noApplications: 'No new applications', viewAll: 'View All',
    },
    bookings: {
      title: 'Bookings', myTitle: 'My Bookings', bookingNum: 'Booking #', client: 'Client',
      eventDate: 'Event Date', guests: 'Guests', total: 'Total Amount',
      dateFrom: 'From', dateTo: 'To', newBooking: 'New Booking',
    },
    bookingDetail: {
      title: 'Booking Detail', eventDate: 'Event Date',
      guestCount: 'Guest Count', guestSuffix: 'guests', notes: 'Notes',
      orderItems: 'Order Items', adminAction: 'Admin Action', adminNotes: 'Admin Notes',
      approve: 'Approve', reject: 'Reject',
      confirmApproveTitle: 'Approve Booking',
      confirmApproveText: 'Approve this booking? An invoice will be created automatically.',
      successApprove: 'Booking approved and invoice created!',
      successReject: 'Booking rejected.',
    },
    newBooking: {
      title: 'New Booking', step1: 'Basic', step2: 'Menu', step3: 'Services', step4: 'Confirm',
      eventDate: 'Event Date', eventTime: 'Time', guestCount: 'Guest Count',
      notesOpt: 'Notes (optional)', quantity: 'Quantity', noItems: 'No items selected',
      submitBooking: 'Submit Booking Request',
      successMsg: 'Booking request submitted successfully!',
    },
    invoices: {
      title: 'Invoices', detail: 'Invoice Detail', num: 'Invoice #',
      client: 'Client', date: 'Date', totalAmount: 'Total Amount', paidAmount: 'Paid',
      balance: 'Balance', dueDate: 'Due Date', paymentStatus: 'Payment Status',
      subtotal: 'Subtotal', downloadPdf: 'Download PDF', booking: 'Booking',
      payDue: 'Due Date', orderItems: 'Order Items',
      fileNotFound: 'File not found or not yet created',
    },
    payments: {
      title: 'Payments', history: 'Payment History', invoicePayments: 'Invoice Payments',
      deposits: 'Deposits', addTab: '+ Add Payment', num: 'Payment #',
      date: 'Date', type: 'Type', method: 'Method', addNew: 'Add New Payment',
      paymentType: 'Payment Type', client: 'Client', amountLabel: 'Amount (UZS)',
      paymentDate: 'Payment Date', bankRef: 'Bank Reference (optional)',
      notesOpt: 'Notes (optional)', savePayment: 'Save Payment',
      lastUpdated: 'Last Updated', invoiceType: 'Invoice Payment', depositType: 'Deposit',
      successMsg: 'Payment saved!',
    },
    clients: {
      title: 'Clients', detail: 'Client Detail', company: 'Company',
      name: 'Name', phone: 'Phone', director: 'Director', inn: 'Tax ID',
      bank: 'Bank', accountNum: 'Account Number', address: 'Address', contract: 'Contract',
      financialStatus: 'Financial Status', depositBalance: 'Deposit Balance',
      bookings: 'Bookings', approve: 'Approve', block: 'Block',
      successApprove: 'Client approved!', successBlock: 'Client blocked!',
    },
    contract: {
      title: 'Contract', signed: 'Contract Signed',
      signedDesc: 'You have successfully signed the contract.',
      warningText: 'You must sign the contract to access menu and booking features.',
      download: 'Download Contract', confirmTitle: 'Confirm Contract',
      agreeCheckbox: 'I have read and agree to the contract terms',
      noContract: 'No active contract available. Contact admin.',
      successMsg: 'Contract signed successfully!',
      downloadError: 'Error downloading file',
    },
    menu: {
      title: 'Menu', minOrder: 'Min Order',
      label: 'OUR MENU', sectionTitle: 'Our Dishes', currency: 'UZS',
    },
    services: {
      title: 'Services', newService: 'New Service', edit: 'Edit',
      description: 'Description', priceLabel: 'Price (UZS)', unit: 'Unit',
      active: 'Active', inactive: 'Inactive',
      added: 'Service added!', updated: 'Service updated!', deleted: 'Service deleted!',
    },
    reports: {
      title: 'Reports', clientsReport: 'Clients Report', invoicesReport: 'Invoices Report',
      export: 'Export', generate: 'Generate Report',
    },
    landingSettings: { title: 'Landing Page Settings', save: 'Save', preview: 'Preview' },
    menuManage: {
      title: 'Menu Management', addCategory: 'Add Category', addItem: 'Add Item',
      categoryName: 'Category Name', edit: 'Edit', delete: 'Delete',
    },
    hero: {
      location: 'KHIVA, UZBEKISTAN', subtitle: 'The taste of Khiva history',
      partner: 'Become a Partner', viewMenu: 'View Menu',
    },
    features: { label: 'OUR ADVANTAGES', title: 'Why OldKhiva?' },
    about: { label: 'ABOUT US', cta: 'Become a Partner' },
    cta: { title: 'Become a Partner' },
    contact: {
      label: 'CONTACT', title: 'Contact Us', phone: 'Phone',
      email: 'Email', address: 'Address',
    },
    footer: { rights: 'All rights reserved.' },
  },

  ru: {
    common: {
      logout: 'Выйти', save: 'Сохранить', cancel: 'Отмена', confirm: 'Подтвердить',
      back: 'Назад', next: 'Продолжить', close: 'Закрыть', search: 'Поиск',
      all: 'Все', status: 'Статус', date: 'Дата', amount: 'Сумма', total: 'Итого',
      balance: 'Остаток', paid: 'Оплачено', notes: 'Примечание', name: 'Название',
      price: 'Цена', quantity: 'Кол-во', currency: 'сум', new: 'Новый',
      pending: 'ожидает', submit: 'Отправить', view: 'Просмотр',
    },
    bookingStatus: {
      pending: 'Ожидает', approved: 'Подтверждён', rejected: 'Отклонён',
      cancelled: 'Отменён', completed: 'Завершён',
    },
    invoiceStatus: {
      unpaid: 'Не оплачен', partial: 'Частично', paid: 'Оплачен', overdue: 'Просрочен',
    },
    paymentMethod: {
      cash: 'Наличные', bank_transfer: 'Банковский перевод', card: 'Карта',
    },
    nav: {
      home: 'Главная', menu: 'Меню', about: 'О нас', contact: 'Контакт',
      login: 'Войти', register: 'Регистрация',
    },
    adminNav: {
      dashboard: 'Dashboard', clients: 'Клиенты', contracts: 'Договоры',
      menu: 'Меню', services: 'Услуги', bookings: 'Бронирования', invoices: 'Счета',
      payments: 'Оплаты', reports: 'Отчёты', landingPage: 'Landing Page',
    },
    clientNav: {
      dashboard: 'Главная', contract: 'Договор', menu: 'Меню',
      newBooking: 'Забронировать', myBookings: 'Мои брони', invoices: 'Счета',
      payments: 'Оплаты',
    },
    login: {
      title: 'Вход в систему', email: 'Email', password: 'Пароль', submit: 'Войти',
      noAccount: 'Нет аккаунта?', register: 'Зарегистрироваться',
      success: 'Вы успешно вошли!', error: 'Произошла ошибка',
    },
    register: {
      title: 'Регистрация', subtitle: 'Зарегистрируйтесь как туристическая фирма',
      step1: 'Основное', step2: 'Реквизиты', step3: 'Подтверждение',
      fullName: 'Полное имя', companyName: 'Название компании', phone: 'Телефон',
      password: 'Пароль', confirmPassword: 'Подтвердите пароль',
      directorName: 'Директор', inn: 'ИНН/СТИР', bankName: 'Банк',
      mfo: 'МФО', bankAccount: 'Расчётный счёт', address: 'Адрес',
      company: 'Компания', confirmData: 'Подтверждаю, что все данные верны',
      hasAccount: 'Уже есть аккаунт?', login: 'Войти',
      successMsg: 'Заявка принята! Ожидайте подтверждения администратора.',
      error: 'Произошла ошибка',
    },
    dashboard: {
      title: 'Главная', balance: 'Баланс', totalBookings: 'Всего броней',
      pending: 'Ожидает', unpaidInvoices: 'Не оплачено', recentBookings: 'Последние брони',
      quickActions: 'Быстрые действия', newBooking: 'Новое бронирование',
      viewInvoices: 'Счета', viewMenu: 'Смотреть меню',
    },
    adminDashboard: {
      title: 'Dashboard', totalClients: 'Всего клиентов', totalBookings: 'Всего броней',
      totalRevenue: 'Общий доход', unpaidInvoices: 'Неоплаченные счета',
      recentBookings: 'Последние брони', newApplications: 'Новые заявки',
      new: 'Новый', noApplications: 'Нет новых заявок', viewAll: 'Все',
    },
    bookings: {
      title: 'Бронирования', myTitle: 'Мои брони', bookingNum: 'Брон #', client: 'Клиент',
      eventDate: 'Дата мероприятия', guests: 'Гостей', total: 'Общая сумма',
      dateFrom: 'С', dateTo: 'По', newBooking: 'Новое бронирование',
    },
    bookingDetail: {
      title: 'Детали бронирования', eventDate: 'Дата мероприятия',
      guestCount: 'Кол-во гостей', guestSuffix: 'чел.', notes: 'Примечание',
      orderItems: 'Состав заказа', adminAction: 'Действие администратора',
      adminNotes: 'Примечание администратора', approve: 'Подтвердить', reject: 'Отклонить',
      confirmApproveTitle: 'Подтверждение брони',
      confirmApproveText: 'Подтвердить это бронирование? Счёт будет создан автоматически.',
      successApprove: 'Бронирование подтверждено и счёт создан!',
      successReject: 'Бронирование отклонено.',
    },
    newBooking: {
      title: 'Новое бронирование', step1: 'Основное', step2: 'Меню', step3: 'Услуги', step4: 'Подтверждение',
      eventDate: 'Дата мероприятия', eventTime: 'Время', guestCount: 'Кол-во гостей',
      notesOpt: 'Примечание (необязательно)', quantity: 'Кол-во', noItems: 'Ничего не выбрано',
      submitBooking: 'Отправить заявку',
      successMsg: 'Заявка на бронирование успешно отправлена!',
    },
    invoices: {
      title: 'Счета', detail: 'Детали счёта', num: 'Счёт #',
      client: 'Клиент', date: 'Дата', totalAmount: 'Общая сумма', paidAmount: 'Оплачено',
      balance: 'Остаток', dueDate: 'Срок', paymentStatus: 'Статус оплаты',
      subtotal: 'Подытог', downloadPdf: 'Скачать PDF', booking: 'Бронь',
      payDue: 'Срок оплаты', orderItems: 'Состав заказа',
      fileNotFound: 'Файл не найден или ещё не создан',
    },
    payments: {
      title: 'Оплаты', history: 'История оплат', invoicePayments: 'Оплаты по счетам',
      deposits: 'Депозиты', addTab: '+ Добавить оплату', num: 'Оплата #',
      date: 'Дата', type: 'Тип', method: 'Способ', addNew: 'Добавить оплату',
      paymentType: 'Тип оплаты', client: 'Клиент', amountLabel: 'Сумма (сум)',
      paymentDate: 'Дата оплаты', bankRef: 'Банковский референс (необязательно)',
      notesOpt: 'Примечание (необязательно)', savePayment: 'Сохранить оплату',
      lastUpdated: 'Последнее обновление', invoiceType: 'Оплата по счёту', depositType: 'Депозит',
      successMsg: 'Оплата сохранена!',
    },
    clients: {
      title: 'Клиенты', detail: 'Детали клиента', company: 'Компания',
      name: 'Имя', phone: 'Телефон', director: 'Директор', inn: 'ИНН',
      bank: 'Банк', accountNum: 'Расчётный счёт', address: 'Адрес', contract: 'Договор',
      financialStatus: 'Финансовое состояние', depositBalance: 'Баланс депозита',
      bookings: 'Бронирования', approve: 'Подтвердить', block: 'Заблокировать',
      successApprove: 'Клиент подтверждён!', successBlock: 'Клиент заблокирован!',
    },
    contract: {
      title: 'Договор', signed: 'Договор подписан',
      signedDesc: 'Вы успешно подписали договор.',
      warningText: 'Необходимо подписать договор для доступа к меню и бронированию.',
      download: 'Скачать договор', confirmTitle: 'Подтвердить договор',
      agreeCheckbox: 'Я ознакомился и согласен с условиями договора',
      noContract: 'Нет активного договора. Свяжитесь с администратором.',
      successMsg: 'Договор успешно подписан!',
      downloadError: 'Ошибка при скачивании',
    },
    menu: {
      title: 'Меню', minOrder: 'Мин. заказ',
      label: 'НАШЕ МЕНЮ', sectionTitle: 'Наши блюда', currency: 'сум',
    },
    services: {
      title: 'Услуги', newService: 'Новая услуга', edit: 'Редактировать',
      description: 'Описание', priceLabel: 'Цена (сум)', unit: 'Ед. изм.',
      active: 'Активен', inactive: 'Неактивен',
      added: 'Услуга добавлена!', updated: 'Услуга обновлена!', deleted: 'Услуга удалена!',
    },
    reports: {
      title: 'Отчёты', clientsReport: 'Отчёт по клиентам', invoicesReport: 'Отчёт по счетам',
      export: 'Экспорт', generate: 'Создать отчёт',
    },
    landingSettings: { title: 'Настройки Landing Page', save: 'Сохранить', preview: 'Просмотр' },
    menuManage: {
      title: 'Управление меню', addCategory: 'Добавить категорию', addItem: 'Добавить блюдо',
      categoryName: 'Название категории', edit: 'Редактировать', delete: 'Удалить',
    },
    hero: {
      location: 'ХИВА, УЗБЕКИСТАН', subtitle: 'Вкус истории Хивы',
      partner: 'Стать партнёром', viewMenu: 'Смотреть меню',
    },
    features: { label: 'НАШИ ПРЕИМУЩЕСТВА', title: 'Почему OldKhiva?' },
    about: { label: 'О НАС', cta: 'Стать партнёром' },
    cta: { title: 'Стать партнёром' },
    contact: {
      label: 'КОНТАКТ', title: 'Свяжитесь с нами', phone: 'Телефон',
      email: 'Email', address: 'Адрес',
    },
    footer: { rights: 'Все права защищены.' },
  },

  de: {
    common: {
      logout: 'Abmelden', save: 'Speichern', cancel: 'Abbrechen', confirm: 'Bestätigen',
      back: 'Zurück', next: 'Weiter', close: 'Schließen', search: 'Suchen',
      all: 'Alle', status: 'Status', date: 'Datum', amount: 'Betrag', total: 'Gesamt',
      balance: 'Restbetrag', paid: 'Bezahlt', notes: 'Notizen', name: 'Name',
      price: 'Preis', quantity: 'Menge', currency: 'UZS', new: 'Neu',
      pending: 'ausstehend', submit: 'Absenden', view: 'Anzeigen',
    },
    bookingStatus: {
      pending: 'Ausstehend', approved: 'Bestätigt', rejected: 'Abgelehnt',
      cancelled: 'Storniert', completed: 'Abgeschlossen',
    },
    invoiceStatus: {
      unpaid: 'Unbezahlt', partial: 'Teilweise', paid: 'Bezahlt', overdue: 'Überfällig',
    },
    paymentMethod: {
      cash: 'Bar', bank_transfer: 'Banküberweisung', card: 'Karte',
    },
    nav: {
      home: 'Startseite', menu: 'Speisekarte', about: 'Über uns', contact: 'Kontakt',
      login: 'Anmelden', register: 'Registrieren',
    },
    adminNav: {
      dashboard: 'Dashboard', clients: 'Kunden', contracts: 'Verträge',
      menu: 'Speisekarte', services: 'Leistungen', bookings: 'Buchungen', invoices: 'Rechnungen',
      payments: 'Zahlungen', reports: 'Berichte', landingPage: 'Landing Page',
    },
    clientNav: {
      dashboard: 'Startseite', contract: 'Vertrag', menu: 'Speisekarte',
      newBooking: 'Buchen', myBookings: 'Meine Buchungen', invoices: 'Rechnungen',
      payments: 'Zahlungen',
    },
    login: {
      title: 'Anmelden', email: 'E-Mail', password: 'Passwort', submit: 'Anmelden',
      noAccount: 'Kein Konto?', register: 'Registrieren',
      success: 'Erfolgreich angemeldet!', error: 'Ein Fehler ist aufgetreten',
    },
    register: {
      title: 'Registrieren', subtitle: 'Als Reiseagentur registrieren',
      step1: 'Grunddaten', step2: 'Details', step3: 'Bestätigung',
      fullName: 'Vollständiger Name', companyName: 'Firmenname', phone: 'Telefon',
      password: 'Passwort', confirmPassword: 'Passwort bestätigen',
      directorName: 'Direktor', inn: 'Steuer-ID', bankName: 'Bank',
      mfo: 'MFO', bankAccount: 'Kontonummer', address: 'Adresse',
      company: 'Firma', confirmData: 'Ich bestätige, dass alle Angaben korrekt sind',
      hasAccount: 'Bereits registriert?', login: 'Anmelden',
      successMsg: 'Antrag eingereicht! Warten Sie auf die Bestätigung des Admins.',
      error: 'Ein Fehler ist aufgetreten',
    },
    dashboard: {
      title: 'Startseite', balance: 'Guthaben', totalBookings: 'Buchungen gesamt',
      pending: 'Ausstehend', unpaidInvoices: 'Unbezahlt', recentBookings: 'Letzte Buchungen',
      quickActions: 'Schnellaktionen', newBooking: 'Neue Buchung',
      viewInvoices: 'Rechnungen', viewMenu: 'Speisekarte ansehen',
    },
    adminDashboard: {
      title: 'Dashboard', totalClients: 'Kunden gesamt', totalBookings: 'Buchungen gesamt',
      totalRevenue: 'Gesamtumsatz', unpaidInvoices: 'Unbezahlte Rechnungen',
      recentBookings: 'Letzte Buchungen', newApplications: 'Neue Anfragen',
      new: 'Neu', noApplications: 'Keine neuen Anfragen', viewAll: 'Alle',
    },
    bookings: {
      title: 'Buchungen', myTitle: 'Meine Buchungen', bookingNum: 'Buchung #', client: 'Kunde',
      eventDate: 'Veranstaltungsdatum', guests: 'Gäste', total: 'Gesamtbetrag',
      dateFrom: 'Von', dateTo: 'Bis', newBooking: 'Neue Buchung',
    },
    bookingDetail: {
      title: 'Buchungsdetail', eventDate: 'Veranstaltungsdatum',
      guestCount: 'Gästeanzahl', guestSuffix: 'Pers.', notes: 'Notizen',
      orderItems: 'Bestellpositionen', adminAction: 'Admin-Aktion', adminNotes: 'Admin-Notizen',
      approve: 'Bestätigen', reject: 'Ablehnen',
      confirmApproveTitle: 'Buchung bestätigen',
      confirmApproveText: 'Diese Buchung bestätigen? Eine Rechnung wird automatisch erstellt.',
      successApprove: 'Buchung bestätigt und Rechnung erstellt!',
      successReject: 'Buchung abgelehnt.',
    },
    newBooking: {
      title: 'Neue Buchung', step1: 'Grunddaten', step2: 'Speisekarte', step3: 'Leistungen', step4: 'Bestätigung',
      eventDate: 'Veranstaltungsdatum', eventTime: 'Uhrzeit', guestCount: 'Gästeanzahl',
      notesOpt: 'Notizen (optional)', quantity: 'Menge', noItems: 'Keine Artikel ausgewählt',
      submitBooking: 'Buchungsanfrage senden',
      successMsg: 'Buchungsanfrage erfolgreich gesendet!',
    },
    invoices: {
      title: 'Rechnungen', detail: 'Rechnungsdetail', num: 'Rechnung #',
      client: 'Kunde', date: 'Datum', totalAmount: 'Gesamtbetrag', paidAmount: 'Bezahlt',
      balance: 'Restbetrag', dueDate: 'Fälligkeit', paymentStatus: 'Zahlungsstatus',
      subtotal: 'Zwischensumme', downloadPdf: 'PDF herunterladen', booking: 'Buchung',
      payDue: 'Zahlungsfrist', orderItems: 'Bestellpositionen',
      fileNotFound: 'Datei nicht gefunden oder noch nicht erstellt',
    },
    payments: {
      title: 'Zahlungen', history: 'Zahlungsverlauf', invoicePayments: 'Rechnungszahlungen',
      deposits: 'Einlagen', addTab: '+ Zahlung hinzufügen', num: 'Zahlung #',
      date: 'Datum', type: 'Typ', method: 'Methode', addNew: 'Neue Zahlung hinzufügen',
      paymentType: 'Zahlungstyp', client: 'Kunde', amountLabel: 'Betrag (UZS)',
      paymentDate: 'Zahlungsdatum', bankRef: 'Bankreferenz (optional)',
      notesOpt: 'Notizen (optional)', savePayment: 'Zahlung speichern',
      lastUpdated: 'Zuletzt aktualisiert', invoiceType: 'Rechnungszahlung', depositType: 'Einlage',
      successMsg: 'Zahlung gespeichert!',
    },
    clients: {
      title: 'Kunden', detail: 'Kundendetail', company: 'Firma',
      name: 'Name', phone: 'Telefon', director: 'Direktor', inn: 'Steuer-ID',
      bank: 'Bank', accountNum: 'Kontonummer', address: 'Adresse', contract: 'Vertrag',
      financialStatus: 'Finanzstatus', depositBalance: 'Einlagensaldo',
      bookings: 'Buchungen', approve: 'Bestätigen', block: 'Blockieren',
      successApprove: 'Kunde bestätigt!', successBlock: 'Kunde blockiert!',
    },
    contract: {
      title: 'Vertrag', signed: 'Vertrag unterzeichnet',
      signedDesc: 'Sie haben den Vertrag erfolgreich unterzeichnet.',
      warningText: 'Sie müssen den Vertrag unterzeichnen, um auf Menü und Buchungen zuzugreifen.',
      download: 'Vertrag herunterladen', confirmTitle: 'Vertrag bestätigen',
      agreeCheckbox: 'Ich habe die Vertragsbedingungen gelesen und stimme zu',
      noContract: 'Kein aktiver Vertrag verfügbar. Kontaktieren Sie den Admin.',
      successMsg: 'Vertrag erfolgreich unterzeichnet!',
      downloadError: 'Fehler beim Herunterladen',
    },
    menu: {
      title: 'Speisekarte', minOrder: 'Mindestbestellung',
      label: 'UNSERE SPEISEKARTE', sectionTitle: 'Unsere Gerichte', currency: 'UZS',
    },
    services: {
      title: 'Leistungen', newService: 'Neue Leistung', edit: 'Bearbeiten',
      description: 'Beschreibung', priceLabel: 'Preis (UZS)', unit: 'Einheit',
      active: 'Aktiv', inactive: 'Inaktiv',
      added: 'Leistung hinzugefügt!', updated: 'Leistung aktualisiert!', deleted: 'Leistung gelöscht!',
    },
    reports: {
      title: 'Berichte', clientsReport: 'Kundenbericht', invoicesReport: 'Rechnungsbericht',
      export: 'Exportieren', generate: 'Bericht erstellen',
    },
    landingSettings: { title: 'Landing Page Einstellungen', save: 'Speichern', preview: 'Vorschau' },
    menuManage: {
      title: 'Speisekartenverwaltung', addCategory: 'Kategorie hinzufügen', addItem: 'Gericht hinzufügen',
      categoryName: 'Kategoriename', edit: 'Bearbeiten', delete: 'Löschen',
    },
    hero: {
      location: 'CHIWA, USBEKISTAN', subtitle: 'Der Geschmack der Geschichte Chiwas',
      partner: 'Partner werden', viewMenu: 'Speisekarte ansehen',
    },
    features: { label: 'UNSERE VORTEILE', title: 'Warum OldKhiva?' },
    about: { label: 'ÜBER UNS', cta: 'Partner werden' },
    cta: { title: 'Partner werden' },
    contact: {
      label: 'KONTAKT', title: 'Kontaktieren Sie uns', phone: 'Telefon',
      email: 'E-Mail', address: 'Adresse',
    },
    footer: { rights: 'Alle Rechte vorbehalten.' },
  },
}

export const useI18nStore = defineStore('i18n', () => {
  const locale = ref(localStorage.getItem('lang') || 'uz')

  const currentLang = computed(() => languages.find(l => l.code === locale.value) || languages[0])

  function t(key) {
    return key.split('.').reduce((obj, k) => obj?.[k], translations[locale.value]) ?? key
  }

  function setLocale(code) {
    locale.value = code
    localStorage.setItem('lang', code)
  }

  return { locale, currentLang, t, setLocale }
})
