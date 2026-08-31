import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

export function RecentBarangs({ data = [] }: { data: any[] }) {
  if (!data || data.length === 0) {
    return <div className='text-muted-foreground text-sm'>Belum ada data barang.</div>
  }

  return (
    <div className='space-y-8'>
      {data.map((barang, index) => (
        <div key={index} className='flex items-center gap-4'>
          <Avatar className='h-9 w-9'>
            <AvatarFallback>{barang.nama_barang.substring(0, 2).toUpperCase()}</AvatarFallback>
          </Avatar>
          <div className='flex flex-1 flex-wrap items-center justify-between'>
            <div className='space-y-1'>
              <p className='text-sm leading-none font-medium'>{barang.nama_barang}</p>
              <p className='text-sm text-muted-foreground'>
                {barang.kategori}
              </p>
            </div>
            <div className='font-medium'>{barang.jumlah} Unit</div>
          </div>
        </div>
      ))}
    </div>
  )
}
