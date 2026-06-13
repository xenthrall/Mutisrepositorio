<?php

return [
    'FINANCIAL' => [

        'Facturación y Punto de Venta (POS)' => [
            [
                'name' => 'financial.invoices.manage',
                'action' => 'Gestionar facturas y Punto de Venta',
                'description' => 'Permite acceder al POS, crear borradores de factura, agregar líneas de pedido y visualizar facturas existentes.',
                'is_system' => false,
                'criticality' => 'medium',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR', 'SECRETARIA']
            ],
            [
                'name' => 'financial.invoices.delete',
                'action' => 'Anular y eliminar facturas',
                'description' => 'Permite la anulación o eliminación de facturas emitidas. Acción destructiva y delicada a nivel contable.',
                'is_system' => false,
                'criticality' => 'high',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR']
            ],
        ],

        'Tesorería (Ingresos y Egresos)' => [
            [
                'name' => 'financial.payments.manage',
                'action' => 'Procesar pagos y recaudos',
                'description' => 'Permite registrar el ingreso de dinero en cajas o bancos para liquidar facturas (cobrar).',
                'is_system' => false,
                'criticality' => 'high',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR', 'SECRETARIA']
            ],
            [
                'name' => 'financial.expenses.manage',
                'action' => 'Registrar egresos y gastos',
                'description' => 'Permite registrar la salida de dinero de los diarios para pagos a proveedores, servicios o compras.',
                'is_system' => false,
                'criticality' => 'high',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR', 'SECRETARIA']
            ],
        ],

        'Análisis y Reportes' => [
            [
                'name' => 'financial.dashboard.view',
                'action' => 'Ver Flujo de Caja y Libro Mayor',
                'description' => 'Otorga acceso al dashboard gerencial para visualizar ingresos, egresos, saldos consolidados y gráficas financieras.',
                'is_system' => false,
                'criticality' => 'high', 
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR']
            ],
        ],

        'Catálogos Financieros' => [
            [
                'name' => 'financial.products.manage',
                'action' => 'Gestionar productos y servicios',
                'description' => 'Permite crear, editar y cambiar el precio de los productos o servicios (mensualidades, uniformes) que se ofrecen en el POS.',
                'is_system' => false,
                'criticality' => 'medium',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR', 'SECRETARIA']
            ],
            [
                'name' => 'financial.expense_categories.manage',
                'action' => 'Gestionar categorías de gastos',
                'description' => 'Permite definir las tipologías para clasificar los egresos de la institución (ej. Papelería, Servicios Públicos).',
                'is_system' => false,
                'criticality' => 'low',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR', 'SECRETARIA']
            ],
        ],

        'Configuración Contable' => [
            [
                'name' => 'financial.journals.manage',
                'action' => 'Gestionar Cajas y Bancos (Diarios)',
                'description' => 'Permite crear cuentas bancarias o cajas registradoras, editar sus datos y modificar saldos iniciales.',
                'is_system' => false,
                'criticality' => 'high',
                'roles' => ['SUPER ADMIN', 'ADMIN', 'RECTOR']
            ],
        ],

    ],
];