import { zodResolver } from '@hookform/resolvers/zod'
import { useQuery } from '@tanstack/react-query'
import {
  Bar,
  BarChart,
  CartesianGrid,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from 'recharts'
import { useState } from 'react'
import type { ReactNode } from 'react'
import { useForm } from 'react-hook-form'
import {
  Cloud,
  Droplets,
  Layers,
  Search,
  Thermometer,
  Users,
  Wind,
} from 'lucide-react'
import { getErrorMessage } from '../../../shared/api/errors'
import { Button } from '../../../shared/components/Button'
import { Card } from '../../../shared/components/Card'
import { Input } from '../../../shared/components/Input'
import { StateMessage } from '../../../shared/components/StateMessage'
import { getClima, getResumen } from '../api/dashboard.api'
import { climaSchema } from '../schemas/clima.schema'
import type { ClimaFormValues } from '../schemas/clima.schema'

export function DashboardPage() {
  const [ciudad, setCiudad] = useState('Madrid')

  const resumenQuery = useQuery({
    queryKey: ['dashboard', 'resumen'],
    queryFn: getResumen,
  })

  const climaQuery = useQuery({
    queryKey: ['dashboard', 'clima', ciudad],
    queryFn: () => getClima(ciudad),
    enabled: Boolean(ciudad),
    retry: false,
  })

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<ClimaFormValues>({
    resolver: zodResolver(climaSchema),
    defaultValues: { ciudad },
  })

  function onSubmit(values: ClimaFormValues) {
    setCiudad(values.ciudad)
  }

  const totales = resumenQuery.data?.totales

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Dashboard</h1>
        <p className="mt-1 text-sm text-slate-600">Resumen general del club.</p>
      </div>

      {resumenQuery.isPending && (
        <StateMessage kind="loading" message="Cargando resumen..." />
      )}
      {resumenQuery.isError && (
        <StateMessage
          kind="error"
          message={getErrorMessage(resumenQuery.error)}
        />
      )}

      {totales && (
        <div className="grid gap-4 sm:grid-cols-3">
          <StatCard
            label="Deportistas"
            value={totales.deportistas}
            icon={<Users className="size-5 text-slate-500" />}
          />
          <StatCard
            label="Modalidades"
            value={totales.modalidades}
            icon={<Layers className="size-5 text-slate-500" />}
          />
          <StatCard
            label="Usuarios"
            value={totales.usuarios}
            icon={<Users className="size-5 text-slate-500" />}
          />
        </div>
      )}

      <div className="grid gap-6 lg:grid-cols-2">
        <Card title="Deportistas por modalidad">
          {resumenQuery.data?.por_modalidad.length ? (
            <div className="h-64 w-full">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={resumenQuery.data.por_modalidad}>
                  <CartesianGrid strokeDasharray="3 3" vertical={false} />
                  <XAxis
                    dataKey="modalidad"
                    tick={{ fontSize: 10 }}
                    interval={0}
                    angle={-20}
                    textAnchor="end"
                    height={60}
                  />
                  <YAxis allowDecimals={false} />
                  <Tooltip />
                  <Bar dataKey="total" fill="#0f172a" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          ) : (
            <StateMessage message="No hay datos para mostrar." />
          )}
        </Card>

        <Card title="Clima">
          <form
            onSubmit={handleSubmit(onSubmit)}
            className="flex items-end gap-2"
          >
            <div className="flex-1">
              <Input
                label="Ciudad"
                placeholder="Ej. Madrid"
                error={errors.ciudad?.message}
                {...register('ciudad')}
              />
            </div>
            <Button
              type="submit"
              disabled={isSubmitting || climaQuery.isFetching}
            >
              <Search className="mr-1 inline size-4" />
              Buscar
            </Button>
          </form>

          <div className="mt-5">
            {climaQuery.isPending && (
              <StateMessage kind="loading" message="Consultando clima..." />
            )}
            {climaQuery.isError && (
              <StateMessage
                kind="error"
                message={getErrorMessage(climaQuery.error)}
              />
            )}
            {climaQuery.data && (
              <div className="space-y-3">
                <div className="flex items-center gap-2">
                  <Cloud className="size-6 text-slate-500" />
                  <div>
                    <p className="font-semibold">
                      {climaQuery.data.ciudad}, {climaQuery.data.pais}
                    </p>
                    <p className="text-xs text-slate-500">
                      Actualizado {climaQuery.data.actualizado_en}
                    </p>
                  </div>
                </div>
                <div className="grid grid-cols-3 gap-3 text-sm">
                  <WeatherMetric
                    icon={<Thermometer className="size-4" />}
                    label="Temperatura"
                    value={`${climaQuery.data.temperatura} °C`}
                  />
                  <WeatherMetric
                    icon={<Droplets className="size-4" />}
                    label="Humedad"
                    value={`${climaQuery.data.humedad} %`}
                  />
                  <WeatherMetric
                    icon={<Wind className="size-4" />}
                    label="Viento"
                    value={`${climaQuery.data.viento} km/h`}
                  />
                </div>
              </div>
            )}
          </div>
        </Card>
      </div>
    </div>
  )
}

function StatCard({
  label,
  value,
  icon,
}: {
  label: string
  value: number
  icon: ReactNode
}) {
  return (
    <Card>
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm text-slate-500">{label}</p>
          <p className="mt-1 text-3xl font-bold">{value}</p>
        </div>
        {icon}
      </div>
    </Card>
  )
}

function WeatherMetric({
  icon,
  label,
  value,
}: {
  icon: ReactNode
  label: string
  value: string
}) {
  return (
    <div className="rounded bg-slate-50 p-3">
      <div className="flex items-center gap-1 text-slate-500">
        {icon}
        <span className="text-xs">{label}</span>
      </div>
      <p className="mt-1 font-semibold">{value}</p>
    </div>
  )
}
